<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use Razorpay\Api\Api;
use App\Models\Branch;
use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\PaypalPayment;
use App\Models\StripePayment;
use App\Models\RazorpayPayment;
use App\Models\FlutterwavePayment;
use App\Models\AdminPayfastPayment;
use App\Models\AdminPaystackPayment;
use App\Events\SendOrderBillEvent;
use App\Models\XenditPayment;
use App\Notifications\SendOrderBill;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentGatewayCredential;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class OrderDetail extends Component
{
    public $restaurant;
    public $shopBranch;
    public $order;
    public $id;
    public $customer;
    public $orderType;
    public $paymentGateway;
    public $razorpayStatus;
    public $stripeStatus;
    public $flutterwaveStatus;
    public $showPaymentModal = false;
    public $paymentOrder;
    public $showQrCode = false;
    public $showPaymentDetail = false;
    public $qrCodeImage;
    public $total;
    public $canAddTip;
    public $tipAmount;
    public $tipNote;
    public $showTipModal = false;
    public $taxMode;
    public $totalTaxAmount = 0;

    use LivewireAlert;

    public function mount()
    {

        $customer = customer();
        $this->order = Order::withoutGlobalScopes()
            ->with(['taxes.tax', 'items', 'items.menuItem'])
            ->where('id', $this->id)
            ->when(optional($customer)->id, fn($query) => $query->where('customer_id', $customer->id))
            ->firstOrFail();

        if ($this->order->customer_id && !$customer) {
            abort(404);
        }

        if (!$customer && $this->restaurant->customer_login_required) {
            return redirect()->route('home');
        }

        $this->shopBranch = request()->filled('branch')
            ? Branch::find(request()->branch)
            : $this->restaurant->branches->first();

        $this->customer = $customer;
        $this->orderType = $this->order->order_type;
        $this->paymentOrder = $this->order;

        $this->paymentGateway = PaymentGatewayCredential::withoutGlobalScopes()->where('restaurant_id', $this->restaurant->id)->first();
        $this->razorpayStatus = (bool)$this->paymentGateway->razorpay_status;
        $this->stripeStatus = (bool)$this->paymentGateway->stripe_status;
        $this->flutterwaveStatus = (bool)$this->paymentGateway->flutterwave_status;

        $this->qrCodeImage = $this->restaurant->qr_code_image;
        $this->canAddTip = $this->restaurant->enable_tip_shop && $this->order->status !== 'paid';
        $this->tipAmount = $this->order->tip_amount;
        $this->tipNote = $this->order->tip_note;

        // Set tax mode and calculate total tax amount
        $this->taxMode = $this->order?->tax_mode ?? ($this->restaurant->tax_mode ?? 'order');

        if ($this->taxMode === 'item') {
            $this->totalTaxAmount = $this->order?->items->sum('tax_amount') ?? 0;
        }
    }


    public function getShouldShowWaiterButtonMobileProperty()
    {

        $this->dispatch('refreshComponent');

        if (!$this->restaurant->is_waiter_request_enabled || !$this->restaurant->is_waiter_request_enabled_on_mobile) {
            return false;
        }

        $cameFromQR = request()->query('hash') === $this->restaurant->hash || request()->boolean('from_qr');

        if ($this->restaurant->is_waiter_request_enabled_open_by_qr && !$cameFromQR) {
            return false;
        }

        return true;
    }


    public function InitializePayment()
    {
        $this->total = floatval($this->paymentOrder->total) - floatval($this->paymentOrder->amount_paid ?: 0);
        $this->showPaymentModal = true;
    }

    public function hidePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function toggleQrCode()
    {
        $this->showQrCode = !$this->showQrCode;
    }

    public function togglePaymentDetail()
    {
        $this->showPaymentDetail = !$this->showPaymentDetail;
    }

    public function initiatePayment($id)
    {
        $payment = RazorpayPayment::create([
            'order_id' => $id,
            'amount' => $this->total
        ]);

        $orderData = [
            'amount' => (int) round($this->total * 100),
            'currency' => $this->restaurant->currency->currency_code
        ];

        $paymentGateway = $this->restaurant->paymentGateways;
        $apiKey = $paymentGateway->razorpay_key;
        $secretKey = $paymentGateway->razorpay_secret;

        $api  = new Api($apiKey, $secretKey);
        $razorpayOrder = $api->order->create($orderData);
        $payment->razorpay_order_id = $razorpayOrder->id;
        $payment->save();

        $this->dispatch('paymentInitiated', payment: $payment);
    }

    public function initiateStripePayment($id)
    {
        $payment = StripePayment::create([
            'order_id' => $id,
            'amount' => $this->total
        ]);

        $this->dispatch('stripePaymentInitiated', payment: $payment);
    }

    #[On('razorpayPaymentCompleted')]
    public function razorpayPaymentCompleted($razorpayPaymentID, $razorpayOrderID, $razorpaySignature)
    {
        $payment = RazorpayPayment::where('razorpay_order_id', $razorpayOrderID)
            ->where('payment_status', 'pending')
            ->first();

        if ($payment) {
            $payment->razorpay_payment_id = $razorpayPaymentID;
            $payment->payment_status = 'completed';
            $payment->payment_date = now()->toDateTimeString();
            $payment->razorpay_signature = $razorpaySignature;
            $payment->save();

            $order = Order::find($payment->order_id);
            $order->amount_paid = floatval($order->amount_paid) + $this->total;
            $order->status = 'paid';
            $order->save();

            Payment::updateOrCreate(
                [
                    'order_id' => $payment->order_id,
                    'payment_method' => 'due',
                    'amount' => $payment->amount
                ],
                [
                    'transaction_id' => $razorpayPaymentID,
                    'payment_method' => 'razorpay',
                    'branch_id' => $this->shopBranch->id
                ]
            );

            $this->sendNotifications($order);

            $this->alert('success', __('messages.paymentDoneSuccessfully'), [
                'toast' => false,
                'position' => 'center',
                'showCancelButton' => true,
                'cancelButtonText' => __('app.close')
            ]);

            $this->redirect(route('order_success', $payment->order->uuid));
        }
    }
    public function initiatePaypalPayment($id)
    {
        $amount = $this->total;
        $currency = strtoupper($this->restaurant->currency->currency_code);

        $unsupportedCurrencies = ['INR'];
        if (in_array($currency, $unsupportedCurrencies)) {
            session()->flash('flash.banner', 'Currency not supported by PayPal.');
            session()->flash('flash.bannerStyle', 'warning');
            return redirect()->route('order_success', $id);
        }

        $clientId = $this->paymentGateway->paypal_client_id;
        $secret = $this->paymentGateway->paypal_secret;

        $paypalPayment = new PaypalPayment();
        $paypalPayment->order_id = $id;
        $paypalPayment->amount = $amount;
        $paypalPayment->payment_status = 'pending';
        $paypalPayment->save();

        $returnUrl = route('paypal.success');
        $cancelUrl = route('paypal.cancel');

        $paypalData = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $currency,
                    "value" => number_format($amount, 2, '.', '')
                ],
                "reference_id" => (string)$paypalPayment->id
            ]],
            "application_context" => [
                "return_url" => $returnUrl,
                "cancel_url" => $cancelUrl
            ]
        ];
        info("Paypal Data: " . json_encode($paypalData));

        $auth = base64_encode("$clientId:$secret");

        $response = Http::withHeaders([
            'Authorization' => "Basic $auth",
            'Content-Type' => 'application/json'
        ])->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', $paypalData);

        if ($response->successful()) {
            $paypalResponse = $response->json();

            $paypalPayment->paypal_payment_id = $paypalResponse['id'];
            $paypalPayment->payment_status = 'pending';
            $paypalPayment->save();

            $approvalLink = collect($paypalResponse['links'])->firstWhere('rel', 'approve')['href'];
            return redirect($approvalLink);
        }
        $paypalPayment->payment_status = 'failed';
        $paypalPayment->save();

        return redirect()->route('paypal.cancel');
    }

    function generateSignature($data, $passPhrase)
    {
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }
        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }

        return md5($getString);
    }

    public function initiatePayfastPayment($id)
    {
        $paymentGateway = $this->restaurant->paymentGateways;
        $isSandbox = $paymentGateway->payfast_mode === 'sandbox';
        $merchantId = $isSandbox ? $paymentGateway->test_payfast_merchant_id : $paymentGateway->payfast_merchant_id;
        $merchantKey = $isSandbox ? $paymentGateway->test_payfast_merchant_key : $paymentGateway->payfast_merchant_key;
        $passphrase = $isSandbox ? $paymentGateway->test_payfast_passphrase : $paymentGateway->payfast_passphrase;
        $amount = number_format($this->total, 2, '.', '');
        $itemName = "Order Payment #$id";
        $reference = 'pf_' . time();
        $data = [
            'merchant_id' => $merchantId,
            'merchant_key' => $merchantKey,
            'return_url' => route('payfast.success', ['reference' => $reference]),
            'cancel_url' => route('payfast.failed', ['reference' => $reference]),
            'notify_url' => route('payfast.notify', ['company' => $this->restaurant->hash, 'reference' => $reference]),

            'name_first' => auth()->user()->name,
            'email_address' => auth()->user()->email,
            'm_payment_id' => $id, // Your internal ID
            'amount' => $amount,
            'item_name' => $itemName,
        ];


        $signature = $this->generateSignature($data, $passphrase);
        $data['signature'] = $signature;

        AdminPayfastPayment::create([
            'order_id' => $id,
            'payfast_payment_id' => $reference,
            'amount' => $amount,
            'payment_status' => 'pending',
        ]);

        $payfastBaseUrl = $isSandbox ? 'https://sandbox.payfast.co.za/eng/process' : 'https://api.payfast.co.za/eng/process';
        $redirectUrl = $payfastBaseUrl . '?' . http_build_query($data);
        return redirect($redirectUrl);
    }

    public function initiatePaystackPayment($id)
    {
        try {
            $paymentGateway = $this->restaurant->paymentGateways;

            $secretKey = $paymentGateway->paystack_secret_data;
            $user = auth()->user();
            $amount = $this->total;
            $reference = "psk_" . time();
            $data = [
                "reference" => $reference,
                "amount" => (int)($amount * 100), // Paystack expects amount in kobo
                "email" => $user->email,
                "currency" =>  $this->restaurant->currency->currency_code,
                "callback_url" => route('paystack.success'),
                "metadata" => [
                    "cancel_action" => route('paystack.failed', ['reference' => $reference])
                ]

            ];

            $response = Http::withHeaders([
                "Authorization" => "Bearer $secretKey",
                "Content-Type" => "application/json"
            ])->post("https://api.paystack.co/transaction/initialize", $data);

            $responseData = $response->json();
            if (isset($responseData['status']) && $responseData['status'] === true) {
                AdminPaystackPayment::create([
                    'order_id' => $id,
                    'paystack_payment_id' => $reference,
                    'amount' => $amount,
                    'payment_status' => 'pending',
                ]);

                return redirect($responseData['data']['authorization_url']);
            } else {

                session()->flash('error', 'Payment initiation failed.');
                return redirect()->route('paystack.failed');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function initiateXenditPayment($id)
    {
        try {
            $paymentGateway = $this->restaurant->paymentGateways;
            $secretKey = $paymentGateway->xendit_secret_key;
            $amount = $this->total;
            $externalId = 'xendit_' . time();

            $user = $this->customer ?? auth()->user();

            $data = [
                'external_id' => $externalId,
                'amount' => $amount,
                'description' => 'Order Payment #' . $id,
                'currency' => $this->restaurant->currency->currency_code,
                'success_redirect_url' => route('xendit.success', ['external' => $externalId]),
                'failure_redirect_url' => route('xendit.failed',  ['external' => $externalId]),
                'payment_methods' => ['CREDIT_CARD', 'BCA', 'BNI', 'BSI', 'BRI', 'MANDIRI', 'OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY'],
                'should_send_email' => true,
                'customer' => [
                    'given_names' => $user->name ?? 'Guest',
                    'email' => $user->email ?? 'guest@example.com',
                    'mobile_number' => $user->phone ?? '+6281234567890',
                ],
                'items' => [
                    [
                        'name' => 'Order #' . $id,
                        'quantity' => 1,
                        'price' => $amount,
                        'category' => 'FOOD_AND_BEVERAGE'
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
                'Content-Type' => 'application/json'
            ])->post('https://api.xendit.co/v2/invoices', $data);

            $responseData = $response->json();


            if ($response->successful() && isset($responseData['id'])) {
                XenditPayment::create([
                    'order_id' => $id,
                    'xendit_payment_id' => $externalId,
                    'xendit_invoice_id' => $responseData['id'],
                    'xendit_external_id' => $externalId,
                    'amount' => $amount,
                    'payment_status' => 'pending',
                ]);

                return redirect($responseData['invoice_url']);
            } else {
                session()->flash('error', 'Xendit payment initiation failed: ' . ($responseData['message'] ?? 'Unknown error'));
                return redirect()->route('xendit.failed');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Xendit payment error: ' . $e->getMessage());
            return redirect()->route('xendit.failed');
        }
    }



    public function makePayment($id, $method)
    {
        if (!$id || !$method) {
            return;
        }

        $allowedMethods = ['cash', 'card', 'upi', 'due', 'others', 'bank_transfer'];

        if (!in_array($method, $allowedMethods)) {
            $this->alert('error', __('messages.invalidPaymentMethod'), [
                'toast' => false,
                'position' => 'center'
            ]);
            return;
        }

        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'pending_verification',
        ]);


        Payment::updateOrCreate(
            [
                'order_id' => $order->id,
                'payment_method' => 'due',
                'amount' => $this->total
            ],
            [
                'payment_method' => $method,
                'branch_id' => $this->shopBranch->id
            ]
        );

        $this->sendNotifications($order);

        $this->alert('success', __('messages.paymentDoneSuccessfully'), [
            'toast' => false,
            'position' => 'center',
            'showCancelButton' => true,
            'cancelButtonText' => __('app.close')
        ]);

        $this->redirect(route('order_success', $order->uuid));
    }


    public function sendNotifications($order)
    {
        if ($order->customer_id) {
            try {
                SendOrderBillEvent::dispatch($order);
            } catch (\Exception $e) {
                \Log::error('Error sending order bill email: ' . $e->getMessage());
            }
        }
    }

    public function addTipModal()
    {
        if ($this->order->status === 'paid') {
            $this->alert('error', __('messages.notHavePermission'), ['toast' => true]);
            return;
        }

        $this->tipAmount = $this->order->tip_amount ?? 0;
        $this->tipNote = $this->order->tip_note ?? '';
        $this->showTipModal = true;
    }

    public function addTip()
    {
        if (!$this->canAddTip) {
            $this->alert('error', __('messages.notHavePermission'), ['toast' => true]);
            return;
        }

        if (!$this->tipAmount || $this->tipAmount <= 0) {
            $this->tipAmount = 0;
        }

        $order = Order::find($this->id);

        $previousTip = floatval($order->tip_amount ?? 0);
        $newTip = floatval($this->tipAmount ?? 0);

        $order->total = floatval($order->total) - $previousTip + $newTip;
        $order->tip_amount = $newTip;
        $order->tip_note = $newTip > 0 ? $this->tipNote : null;
        $order->save();

        $this->order = $order;
        $this->showTipModal = false;

        $message = $newTip > 0 ? __('messages.tipAddedSuccessfully') : __('messages.tipRemovedSuccessfully');
        $this->alert('success', $message, ['toast' => true]);
    }

    public function initiateFlutterwavePayment($id)
    {
        try {
            $paymentGateway = $this->restaurant->paymentGateways;
            $apiSecret = $paymentGateway->flutterwave_secret;
            $amount = $this->total;
            $tx_ref = "txn_" . time();

            $user = $this->customer ?? $this->restaurant;


            $data = [
                "tx_ref" => $tx_ref,
                "amount" => $amount,
                "currency" => $this->restaurant->currency->currency_code,
                "redirect_url" => route('flutterwave.success'),
                "payment_options" => "card",
                "customer" => [
                    "email" => $user->email ?? 'no-email@example.com',
                    "name" => $user->name ?? 'Guest',
                    "phone_number" => $user->phone ?? '0000000000',
                ],
            ];
            $response = Http::withHeaders([
                "Authorization" => "Bearer $apiSecret",
                "Content-Type" => "application/json"
            ])->post("https://api.flutterwave.com/v3/payments", $data);

            $responseData = $response->json();

            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                FlutterwavePayment::create([
                    'order_id' => $id,
                    'flutterwave_payment_id' => $tx_ref,
                    'amount' => $amount,
                    'payment_status' => 'pending',
                ]);

                return redirect($responseData['data']['link']);
            } else {
                return redirect()->route('flutterwave.failed')->withErrors(['error' => 'Payment initiation failed', 'message' => $responseData]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function render()
    {
        return view('livewire.shop.order-detail');
    }

    // Pusher Broadcast
    public function refreshOrderSuccess()
    {
        $this->dispatch('$refresh');
    }
}
