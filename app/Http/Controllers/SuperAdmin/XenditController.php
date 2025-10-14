<?php

namespace App\Http\Controllers\SuperAdmin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use App\Models\SuperadminPaymentGateway;
use App\Models\Restaurant;
use App\Models\Package;
use App\Models\RestaurantPayment;
use App\Models\EmailSetting;
use App\Http\Controllers\Controller;
use App\Notifications\RestaurantUpdatedPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class XenditController extends Controller
{
    private $secretKey;
    private $publicKey;

    /**
     * Load Xendit API credentials from DB
     */
    private function setXenditConfigs(): void
    {
        $gateway = SuperadminPaymentGateway::first();

        if (!$gateway) {
            throw new \Exception("No SuperadminPaymentGateway found");
        }

        $this->secretKey = $gateway->xendit_mode === 'sandbox'
            ? $gateway->test_xendit_secret_key
            : $gateway->live_xendit_secret_key;

        $this->publicKey = $gateway->xendit_mode === 'sandbox'
            ? $gateway->test_xendit_public_key
            : $gateway->live_xendit_public_key;

        if (empty($this->secretKey)) {
            throw new \Exception("Xendit secret key not configured");
        }
    }

    /**
     * Create Xendit customer
     */
    private function createCustomer(Restaurant $restaurant): string
    {
        $payload = [
            'reference_id' => 'cust_' . time(),
            'type' => 'INDIVIDUAL',
            'email' => user()->email ?? 'test@example.com',
            'mobile_number' => user()->phone_number ?? '+639171234567',
            'description' => 'Customer for Restaurant ID ' . $restaurant->id,
            'individual_detail' => [
                'given_names' => user()->name ?? 'Test',
                'surname' => 'User',
                'gender' => 'MALE',
                'date_of_birth' => '1990-01-01',
            ],
        ];

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post('https://api.xendit.co/customers', $payload);

        $data = $response->json();
        Log::info('Xendit Customer Response', $data);

        if (!$response->successful() || !isset($data['id'])) {
            throw new \Exception("Failed to create Xendit customer: " . ($data['message'] ?? 'Unknown error'));
        }

        return $data['id'];
    }

    /**
     * Create Xendit recurring plan
     */
    private function createRecurringPlan(string $externalId, string $customerId, string $type, float $amount, string $currencyCode): array
    {
        $schedule = [
            "reference_id"   => "schedule_" . $externalId,
            "interval"       => $type === "monthly" ? "MONTH" : ($type === "annual" ? "YEAR" : null),
            "interval_count" => $type === "monthly" ? 1 : ($type === "annual" ? 1 : null),
        ];

        $payload = [
            "reference_id"      => $externalId,
            "customer_id"       => $customerId,
            "recurring_action"  => "PAYMENT",
            "currency"          => $currencyCode,
            "amount"            => (float) $amount,
            "schedule"          => $schedule,
            "success_return_url" => route('xendit.subscription.callback'),
            "failure_return_url" => route('xendit.subscription.failed'),
        ];

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post('https://api.xendit.co/recurring/plans', $payload);

        $data = $response->json();
        Log::info('Xendit Plan Response', $data);

        if (!$response->successful() || !isset($data['id'])) {
            throw new \Exception("Failed to create recurring plan: " . ($data['message'] ?? 'Unknown error'));
        }

        return $data;
    }

    /**
     * Initiate Xendit payment
     */
    public function initiateXenditPayment(Request $request)
    {
            $this->setXenditConfigs();

            $package = Package::findOrFail($request->package_id);
            $restaurant = restaurant();

            // validate package type & amount
            $type = $request->input('package_type');

            $amount = $this->determineAmount($package, $type);
            // Cancel previous Xendit subscriptions before creating new one
            $this->cancelPreviousXenditSubscriptions($restaurant);


            $externalId = 'xendit_' . time() . '_' . $package->id;

            if($type === 'lifetime') {

                $packageName = $package->name ?? 'Package #' . $package->id;
                try {
                    $data = [
                        'external_id' => $externalId,
                        'amount' => $amount,
                        'description' => ucfirst($type) . ' Package Subscription - ' . $packageName,
                        'currency' => $package->currency->currency_code,
                        'success_redirect_url' => route('xendit.subscription.callback'),
                        'failure_redirect_url' => route('xendit.subscription.failed'),
                        'payment_methods' => ['CREDIT_CARD', 'BCA', 'BNI', 'BSI', 'BRI', 'MANDIRI', 'OVO', 'DANA', 'LINKAJA', 'SHOPEEPAY'],
                        'should_send_email' => true,
                        'customer' => [
                            'given_names' => $restaurant->name,
                            'email' => $restaurant->email,
                            'mobile_number' => (string) ($restaurant->phone_number ?? '+6281234567890'),
                        ],
                        'items' => [
                            [
                                'name' => $packageName . ' ' . ucfirst($type),
                                'quantity' => 1,
                                'price' => $amount,
                                'category' => strtoupper($type)
                            ]
                        ],
                        'metadata' => [
                            'package_id' => $package->id,
                            'package_type' => $type,
                            'restaurant_id' => $restaurant->id,
                            'payment_id' => $request->payment_id,
                        ]
                    ];

                    $response = Http::withHeaders([
                        'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                        'Content-Type' => 'application/json'
                    ])->post('https://api.xendit.co/v2/invoices', $data);

                    $responseData = $response->json();

                    if ($response->successful() && isset($responseData['id'])) {
                        $subscription = new GlobalSubscription();
                        $subscription->restaurant_id = $restaurant->id;
                        $subscription->package_id = $package->id;
                        $subscription->currency_id = $package->currency_id;
                        $subscription->package_type = $type;
                        $subscription->quantity = 1;
                        $subscription->gateway_name = 'xendit';
                        $subscription->subscription_status = 'inactive';
                        $subscription->subscribed_on_date = now();
                        $subscription->transaction_id = $externalId;
                        $subscription->save();

                        session([
                            'subscription_id' => $subscription->id,
                            'package_amount' => $amount,
                            'payment_id' => $request->payment_id,
                            'xendit_invoice_id' => $responseData['id'],
                            'xendit_external_id' => $externalId,
                        ]);

                        return redirect($responseData['invoice_url']);
                    } else {
                        return redirect()->route('dashboard')->with([
                            'flash.banner' => 'Failed to create payment: ' . ($responseData['message'] ?? 'Unknown error'),
                            'flash.bannerStyle' => 'danger'
                        ]);
                    }

                } catch (\Exception $e) {
                    return redirect()->route('dashboard')->with([
                        'flash.banner' => 'Xendit payment initiation failed: ' . $e->getMessage(),
                        'flash.bannerStyle' => 'danger'
                    ]);
                }
            } else {

                try {
                // 1. Create Customer
                    $customerId = $this->createCustomer($restaurant);

                    // 2. Create Recurring Plan
                    $planData = $this->createRecurringPlan($externalId, $customerId, $type, $amount , $package->currency->currency_code);

                    // 3. Save subscription & invoice
                    $this->createLocalSubscriptionAndInvoice(
                        $restaurant,
                        $package,
                        $type,
                        $amount,
                        $externalId,
                        $customerId,
                        $planData['id']
                    );

                    // 4. Handle redirect if required
                    $authUrl = collect($planData['actions'] ?? [])->first(
                        fn($a) =>
                        $a['action'] === 'AUTH' && $a['url_type'] === 'WEB'
                    )['url'] ?? null;

                    if ($authUrl) {
                        // session([
                        //     'payment_id' => $request->payment_id ?? null,
                        //     'package_amount' => $amount,
                        //     'xendit_recurring_id' => $planData['id'],
                        //     'xendit_external_id'  => $externalId,
                        //     'xendit_customer_id'  => $customerId,
                        //     'subscription_id'     => $subscription->id,
                        //     'invoice_id'          => $invoice->id,
                        // ]);
                        return redirect()->away($authUrl);
                    }

                    return redirect()->route('dashboard')->with([
                        'flash.banner' => 'Recurring plan created successfully',
                        'flash.bannerStyle' => 'success',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Xendit Initiation Error', ['error' => $e->getMessage()]);
                    return redirect()->route('dashboard')->with([
                        'flash.banner' => 'Xendit payment initiation failed: ' . $e->getMessage(),
                        'flash.bannerStyle' => 'danger',
                    ]);
                }
            }
    }

    /**
     * Determine package amount
     */
    private function determineAmount(Package $package, string &$type): float
    {
        if ($package->package_type->value === 'standard') {
            if (!in_array($type, ['monthly', 'annual'])) {
                throw new \Exception("Invalid plan type");
            }
            return $type === 'monthly' ? $package->monthly_price : $package->annual_price;
        }

        $type = 'lifetime';
        return $package->price;
    }

    /**
     * Save subscription & invoice locally
     */
    private function createLocalSubscriptionAndInvoice(
        Restaurant $restaurant,
        Package $package,
        string $type,
        float $amount,
        string $externalId,
        string $customerId,
        string $planId
    ): array {
        $subscription = GlobalSubscription::create([
            'restaurant_id'        => $restaurant->id,
            'package_id'           => $package->id,
            'currency_id'          => $package->currency_id,
            'package_type'         => $type,
            'quantity'             => 1,
            'gateway_name'         => 'xendit',
            'subscription_status'  => 'inactive',
            'subscribed_on_date'   => now(),
            'transaction_id'       => $externalId,
            'customer_id'          => $customerId,
            'subscription_id'      => $planId,
        ]);


        return [$subscription];
    }

    // Callback, failure, and activation methods remain same as your current ones



    /**
     * Handle Xendit payment callback
     */
    public function handleGatewayCallback(Request $request)
    {
        $this->setXenditConfigs();

        $externalId = $request->external_id ?? session('xendit_external_id');
        $recurringId = $request->recurring_id ?? session('xendit_recurring_id');
        $paymentId = session('payment_id');
        $subscriptionId = session('subscription_id');
        $invoiceId = session('invoice_id');
        $packageAmount = session('package_amount');



         if ($subscriptionId) {
            $globalSubscription = GlobalSubscription::find($subscriptionId);

            if ($globalSubscription) {
                // Deactivate other active subscriptions
                GlobalSubscription::where('restaurant_id', $globalSubscription->restaurant_id)
                    ->where('subscription_status', 'active')
                    ->where('id', '!=', $globalSubscription->id)
                    ->update(['subscription_status' => 'inactive', 'ends_at' => now()]);

                // Activate current subscription
                $globalSubscription->subscription_status = 'active';
                $globalSubscription->subscribed_on_date = now();
                $globalSubscription->save();

                Log::info('GlobalSubscription updated to active:', [
                    'id' => $globalSubscription->id,
                    'subscription_status' => $globalSubscription->subscription_status
                ]);

                // Update existing invoice if we have the invoice ID
                if ($invoiceId) {
                    $invoice = GlobalInvoice::find($invoiceId);
                    if ($invoice) {
                        $invoice->status = 'active';
                        $invoice->pay_date = now()->format('Y-m-d');
                        $invoice->save();

                        Log::info('GlobalInvoice updated to active:', [
                            'id' => $invoice->id,
                            'status' => $invoice->status
                        ]);
                    }
                }

                // Update restaurant payment if exists
                if ($paymentId) {
                    $restaurantPayment = RestaurantPayment::find($paymentId);
                    if ($restaurantPayment) {
                        $restaurantPayment->amount = $packageAmount;
                        $restaurantPayment->status = 'paid';
                        $restaurantPayment->payment_date_time = now()->toDateTimeString();
                            $restaurantPayment->transaction_id = $externalId;
                        $restaurantPayment->save();

                    }
                }

                // Update restaurant
                $restaurant = restaurant();
                $restaurant->package_id = $globalSubscription->package_id;
                $restaurant->package_type = $globalSubscription->package_type;
                $restaurant->status = 'active';
                $restaurant->license_expire_on = null;
                $restaurant->save();

                // Send notifications
                $emailSetting = EmailSetting::first();
                if ($emailSetting->mail_driver === 'smtp' && $emailSetting->verified) {
                    $generatedBy = User::withoutGlobalScopes()->whereNull('restaurant_id')->first();
                    Notification::send($generatedBy, new RestaurantUpdatedPlan($restaurant, $globalSubscription->package_id));

                    // Notify restaurant admin
                    $restaurantAdmin = $restaurant->restaurantAdmin($restaurant);
                    Notification::send($restaurantAdmin, new RestaurantUpdatedPlan($restaurant, $globalSubscription->package_id));
                }

                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.planUpgraded'),
                    'flash.bannerStyle' => 'success',
                    'flash.link' => route('settings.index', ['tab' => 'billing'])
                ]);
            }
        }

        // Handle failed payment
        if ($paymentId) {
            $restaurantPayment = RestaurantPayment::find($paymentId);
            if ($restaurantPayment) {
                $restaurantPayment->status = 'failed';
                $restaurantPayment->save();
            }
        }

        return redirect()->route('dashboard')->with([
            'flash.banner' => __('messages.paymentSuccess'),
            'flash.bannerStyle' => 'success'
        ]);
    }

    /**
     * Handle payment failure redirect
     */
    public function paymentFailed(Request $request)
    {

        $paymentId = session('payment_id');

        if ($paymentId) {
            $restaurantPayment = RestaurantPayment::find($paymentId);
            if ($restaurantPayment) {
                $restaurantPayment->status = 'failed';
                $restaurantPayment->save();
            }
        }

        // Clear session
        session()->forget(['subscription_id', 'invoice_id', 'package_amount', 'payment_id', 'xendit_recurring_id', 'xendit_external_id', 'xendit_customer_id']);

        return redirect()->route('dashboard')->with([
            'flash.banner' => 'Payment was cancelled or failed.',
            'flash.bannerStyle' => 'danger'
        ]);
    }


    /**
     * Cancel previous Xendit subscriptions for the restaurant
     */
    public function cancelPreviousXenditSubscriptions(Restaurant $restaurant): void
    {
        try {
            // Find all active Xendit subscriptions for this restaurant
            $activeSubscriptions = GlobalSubscription::where('restaurant_id', $restaurant->id)
                ->where('gateway_name', 'xendit')
                ->where('subscription_status', 'active')
                ->get();

            foreach ($activeSubscriptions as $subscription) {
                // Get the plan_id from the related invoice
                $invoice = GlobalInvoice::where('global_subscription_id', $subscription->id)
                    ->where('gateway_name', 'xendit')
                    ->first();

                if ($invoice && $invoice->plan_id) {
                    try {
                        // Cancel the subscription in Xendit
                        $response = Http::withBasicAuth($this->secretKey, '')
                            ->post("https://api.xendit.co/recurring/plans/{$invoice->plan_id}/deactivate");

                        if ($response->successful()) {
                            Log::info('Xendit: Previous subscription cancelled', [
                                'plan_id' => $invoice->plan_id,
                                'subscription_id' => $subscription->id,
                                'restaurant_id' => $restaurant->id
                            ]);

                            // Mark subscription as cancelled in database
                            $subscription->subscription_status = 'cancelled';
                            $subscription->ends_at = now();
                            $subscription->save();
                        } else {
                            Log::warning('Xendit: Failed to cancel previous subscription', [
                                'plan_id' => $invoice->plan_id,
                                'subscription_id' => $subscription->id,
                                'response' => $response->json()
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Xendit: Error cancelling previous subscription', [
                            'plan_id' => $invoice->plan_id,
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Xendit: Error in cancelPreviousXenditSubscriptions', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
