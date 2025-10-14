<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\XenditPayment;
use App\Models\Order;
use App\Events\SendNewOrderReceived;
use App\Notifications\SendOrderBill;

class XenditPaymentController extends Controller
{
    private $secretKey;
    private $webhookToken;

    /**
     * Set Xendit secret key and webhook token based on restaurant hash.
     */
    private function setKeys(string $societyHash): void
    {
        $restaurant = Restaurant::where('hash', $societyHash)->firstOrFail();
        $this->secretKey = $restaurant->paymentGateways->xendit_secret_key;
    }

        /**
     * Handle Xendit webhook notifications.
     */
   public function handleGatewayWebhook(Request $request, $restaurantHash)
{

    info('Xendit Webhook Callback:', $request->all());

    $this->setKeys($restaurantHash);

    $status = $request->status ?? null;
    $externalId = $request->external_id ?? null;
    $invoiceId = $request->id ?? null;
    if ($status === 'PAID') {
        try {
            $xenditPayment = XenditPayment::where('xendit_payment_id', $externalId)
                ->orWhere('xendit_external_id', $externalId)
                ->first();
            // $payment = XenditPayment::where('xendit_external_id', $externalId)->first();
            if (!$xenditPayment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $xenditPayment->payment_status = 'completed';
            $xenditPayment->payment_date = now();
            $xenditPayment->save();

            $order = Order::find($xenditPayment->order_id);
            $order->amount_paid += $xenditPayment->amount;
            $order->status = 'paid';
            $order->save();

            Payment::updateOrCreate(
                [
                    'order_id' => $xenditPayment->order_id,
                ],
                [
                    'payment_method' => 'xendit',
                    'amount' => $xenditPayment->amount,
                    'transaction_id' => $externalId,
                ]
            );

            return response()->json(['message' => 'Xendit PAID processed']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error handling PAID', 'error' => $e->getMessage()], 400);
        }
    }

    if ($status === 'EXPIRED' || $status === 'FAILED') {
        try {
            $xenditPayment = XenditPayment::where('xendit_payment_id', $externalId)
                ->orWhere('external_id', $externalId)
                ->first();

            if ($xenditPayment) {
                $xenditPayment->payment_status = 'failed';
                $xenditPayment->save();
            }

            return response()->json(['message' => "Xendit {$status} processed"]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error handling failure', 'error' => $e->getMessage()], 400);
        }
    }

    return response()->json(['message' => 'Status not processed']);
}


    /**
     * Success redirect after payment
     */
    public function paymentMainSuccess(Request $request)
    {
        info('Xendit Success Callback:', $request->all());


        $invoiceId = $request->external;
        if (!$invoiceId) {
            return redirect()->route('home')->withErrors(['error' => 'Missing Xendit invoice ID.']);
        }

        $xenditPayment = XenditPayment::where('xendit_payment_id', $invoiceId)->first();

        if ($xenditPayment) {
            $xenditPayment->payment_status = 'completed';
            $xenditPayment->save();
        }

        if ($xenditPayment && $xenditPayment->payment_status === 'completed') {
            $order = Order::find($xenditPayment->order_id);
            $order->amount_paid = $order->amount_paid + $xenditPayment->amount;
            $order->status = 'paid';
            $order->save();

            // Only create a new Payment if transaction_id is not equal to $invoiceId
            $existingPayment = Payment::where('order_id', $xenditPayment->order_id)
                ->where('transaction_id', $invoiceId)
                ->first();

            if (!$existingPayment) {
                Payment::updateOrCreate(
                    [
                        'order_id' => $xenditPayment->order_id,
                        'payment_method' => 'xendit',
                        'amount' => $xenditPayment->amount,
                    ],
                    [
                        'transaction_id' => $invoiceId,
                    ]
                );
            }

            Payment::updateOrCreate(
                [
                    'order_id' => $xenditPayment->order_id,
                    'payment_method' => 'xendit',
                    'amount' => $xenditPayment->amount,
                ],
                [
                    'transaction_id' => $invoiceId,
                ]
            );

            SendNewOrderReceived::dispatch($order);

            if ($order->customer_id) {
                $order->customer->notify(new SendOrderBill($order));
            }

            return redirect()->route('order_success', $xenditPayment->order->uuid)->with([
                'flash.banner' => __('messages.paymentDoneSuccessfully'),
                'flash.bannerStyle' => 'success',
            ]);
        }
    }

    /**
     * Cancel redirect
     */
    public function paymentFailed(Request $request)
    {
        $invoiceId = $request->query('external_id');

        $xenditPayment = XenditPayment::where('xendit_payment_id', $invoiceId)->first();

        if ($xenditPayment) {
            $xenditPayment->payment_status = 'failed';
            $xenditPayment->save();
        }

        session()->flash('flash.banner', 'Payment was cancelled.');
        session()->flash('flash.bannerStyle', 'danger');

        return redirect()->route('dashboard');
    }
}

