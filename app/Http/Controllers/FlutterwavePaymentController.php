<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\FlutterwavePayment;
use App\Events\SendNewOrderReceived;
use App\Models\Restaurant;
use App\Events\SendOrderBillEvent;

class FlutterwavePaymentController extends Controller
{
    private $secretKey;

    public function setKeys($restaurantHash)
    {
        $restaurant = Restaurant::where('hash', $restaurantHash)->first();

        if (!$restaurant) {
            throw new \Exception('Invalid webhook URL. Please check the restaurant hash.');
        }

        $credential = $restaurant->paymentGateways;
        $this->secretKey = $credential->flutterwave_secret;
    }

    public function handleGatewayWebhook(Request $request, $restaurantHash)
    {
        try {
            $this->setKeys($restaurantHash);
            $event = $request->event ?? null;
            $transactionId = $request->data['tx_ref'] ?? null;

            if (!$transactionId || !$event) {
                return response()->json(['message' => 'Invalid webhook payload'], 400);
            }

            $payment = FlutterwavePayment::where('flutterwave_payment_id', $transactionId)->first();

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            switch ($event) {
                case 'charge.completed':
                    $payment->payment_status = 'completed';
                    $payment->save();
                    return response()->json(['message' => 'Payment completed successfully'], 200);
                case 'charge.failed':
                    $payment->payment_status = 'failed';
                    $payment->payment_error_response = data_get($request, 'data.error.message', 'Payment failed');
                    $payment->save();
                    return response()->json(['message' => 'Payment failed'], 200);
                default:
                    return response()->json(['message' => 'Event not processed'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function paymentMainSuccess(Request $request)
    {
        $status = $request->status;
        $transactionId = $request->tx_ref;
        $payment = FlutterwavePayment::where('flutterwave_payment_id', $transactionId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        if ($status !== 'successful') {
            $payment->payment_status = 'failed';
            $payment->payment_error_response = json_encode([
                'code' => $request->error['code'] ?? 'unknown_error',
                'message' => $request->error['message'] ?? 'Payment failed',
            ]);
            $payment->save();

            $orderStatus = $payment->order->status;
            $route = $orderStatus === 'draft' ? redirect()->back() : redirect()->route('order_success', $payment->order->uuid);
            return $route->with([
                'flash.banner' => __('messages.paymentFailed'),
                'flash.bannerStyle' => 'danger',
            ]);
        }


        $payment->payment_status = 'completed';
        $payment->payment_date = now();
        $payment->save();

        if ($payment->payment_status = 'completed') {
            Payment::updateOrCreate(
                [
                    'order_id' => $payment->order_id,
                    'payment_method' => 'due',
                    'amount' => $payment->amount
                ],
                [
                    'payment_method' => 'flutterwave',
                    'branch_id' => $payment->order->branch_id,
                    'transaction_id' => $transactionId,
                ]
            );

            $order = Order::find($payment->order_id);
            $order->amount_paid = $order->amount_paid + $payment->amount;
            $order->status = 'paid';
            $order->save();

            SendNewOrderReceived::dispatch($order);

            if ($order->customer_id) {
                SendOrderBillEvent::dispatch($order);
            }

            return redirect()->route('order_success', $payment->order->uuid)->with([
                'flash.banner' => __('messages.paymentDoneSuccessfully'),
                'flash.bannerStyle' => 'success',
            ]);
        }
    }
}
