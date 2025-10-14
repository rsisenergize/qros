<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AdminPaystackPayment;
use App\Models\Order;
use App\Events\SendNewOrderReceived;
use App\Events\SendOrderBillEvent;

class PaystackPaymentController extends Controller
{
    private $secretKey;

    /**
     * Set Paystack secret key based on restaurant hash.
     */
    private function setKeys(string $societyHash): void
    {
        $restaurant = Restaurant::where('hash', $societyHash)->firstOrFail();
        $this->secretKey = $restaurant->paymentGateways->paystack_secret_data;
    }

    /**
     * Handle Paystack webhook notifications.
     */
    public function handleGatewayWebhook(Request $request, string $societyHash)
    {
        $this->setKeys($societyHash);
        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $reference = $payload['data']['reference'] ?? null;

        if (!$reference) {
            return response()->json(['message' => 'Invalid webhook payload'], 400);
        }

        $payment = AdminPaystackPayment::where('paystack_payment_id', $reference)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($event === 'charge.success') {
            $this->markPaymentAsCompleted($payment);
            return response()->json(['message' => 'Payment successful']);
        }

        if ($event === 'charge.failed') {
            $payment->update([
                'payment_status' => 'failed',
                'payment_error_response' => json_encode($payload),
            ]);
            return response()->json(['message' => 'Payment failed event processed']);
        }

        return response()->json(['message' => 'Event not handled'], 400);
    }

    /**
     * Handle redirect after failed payment.
     */
    public function paymentFailed(Request $request)
    {
        $reference = $request->reference;

        $errorMessage = json_encode([
            'code' => $request->input('error.code', 'unknown_error'),
            'message' => $request->input('error.message', 'Payment failed'),
        ]);

        $payment = AdminPaystackPayment::where('paystack_payment_id', $reference)->first();

        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
                'payment_error_response' => $errorMessage,
            ]);
        }

        session()->flash('flash.banner', 'Payment process failed!');
        session()->flash('flash.bannerStyle', 'danger');

        return redirect(route('shop_restaurant', [$payment->order->branch->restaurant->hash]) . '?branch=' . $payment->order->branch_id);
    }
    /**
     * Handle redirect after successful payment.
     */
    public function paymentMainSuccess(Request $request)
    {
        $reference = $request->reference;
        if (!$reference) {
            return $this->redirectWithMessage('No reference supplied!', 'danger');
        }

        $payment = AdminPaystackPayment::where('paystack_payment_id', $reference)->first();
        if (!$payment) {
            return $this->redirectWithMessage('Payment not found!', 'danger', $payment->order_id);
        }

        $secretKey = auth()->user()->restaurant->paymentGateways->paystack_secret_data;

        $response = Http::withToken($secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if (isset($data['status'], $data['data']) && $data['status'] === true && $data['data']['status'] === 'success') {
            $this->markPaymentAsCompleted($payment, true);
            return $this->redirectWithMessage('Payment processed successfully!', 'success', $payment->order_id);
        }

        // Failed fallback
        $payment->update([
            'payment_status' => 'failed',
            'payment_error_response' => json_encode($data['data'] ?? []),
        ]);

        return $this->redirectWithMessage('Payment process failed!', 'danger', $payment->order_id ?? null);
    }

    /**
     * Mark payment and order as completed, and create a Payment record.
     */
    private function markPaymentAsCompleted(AdminPaystackPayment $payment, bool $createPayment = false): void
    {

        if ($createPayment) {

            Payment::updateOrCreate(
                [
                    'order_id' => $payment->order_id,
                    'payment_method' => 'due',
                    'amount' => $payment->amount,
                ],
                [
                    'payment_method' => 'paystack',
                    'branch_id' => $order->branch_id ?? null,
                    'transaction_id' => $payment->paystack_payment_id,
                ]
            );
        }

        $order = Order::find($payment->order_id);
        $order->amount_paid = $order->amount_paid + $payment->amount;
        $order->status = 'paid';
        $order->save();

        SendNewOrderReceived::dispatch($order);

        if ($order->customer_id) {
            SendOrderBillEvent::dispatch($order);
        }
    }

    /**
     * Utility for flashing message and redirecting.
     */
    private function redirectWithMessage(string $message, string $type, ?int $orderId = null)
    {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $type);
        return redirect()->route('order_success', $orderId);
    }
}
