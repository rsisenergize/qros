<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\PaypalPayment;
use Illuminate\Support\Facades\Log;
use App\Events\SendNewOrderReceived;
use App\Events\SendOrderBillEvent;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentGatewayCredential;


class PaypalPaymentController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $accessToken;

    public function setKeys($restaurantHash)
    {
        $restaurant = Restaurant::where('hash', $restaurantHash)->first();
        if (!$restaurant) {
            throw new \Exception('Invalid webhook URL');
        }

        $credential = $restaurant->paymentGateways;

        $this->clientId = $credential->paypal_client_id;
        $this->clientSecret = $credential->paypal_secret;

        if (is_null($this->clientId) || is_null($this->clientSecret)) {
            throw new \Exception('PayPal credentials are not set correctly.');
        }


    }


    public function handleGatewayWebhook(Request $request, $restaurantHash)
    {
        info('Webhook received', [
            'request' => $request->all(),
        ]);

        $this->setKeys($restaurantHash);

        $event = $request->event_type;
        info('Event type', [
            'event' => $event,
        ]);

        if ($event === 'PAYMENT.CAPTURE.DENIED') {
            try {
            $resource = $request->resource;
            $orderId = $resource['supplementary_data']['related_ids']['order_id'];

            $paypalPayment = PaypalPayment::where('paypal_payment_id', $orderId)->first();

            if ($paypalPayment) {
                $paypalPayment->payment_status = 'failed';
                $paypalPayment->save();
            }

            return response()->json(['message' => 'Payment failed event processed']);
            } catch (\Exception $e) {
            return response()->json(['message' => 'Error handling payment failed event', 'error' => $e->getMessage()], 400);
            }
        }

        if ($event === 'PAYMENT.CAPTURE.COMPLETED') {
            try {
                $resource = $request->resource;
                $orderId = $resource['supplementary_data']['related_ids']['order_id'];
                $transactionId = $resource['id'] ?? null;
                $paypalPayment = PaypalPayment::where('paypal_payment_id', $orderId)->first();

                if (!$paypalPayment) {
                    return response()->json(['message' => 'Payment not found'], 404);
                }

                $paypalPayment->payment_status = 'completed';
                $paypalPayment->payment_date = now();
                $paypalPayment->save();

                $order = Order::find($paypalPayment->order_id);
                $order->cost = $paypalPayment->amount;
                $order->payment_date = $paypalPayment->payment_date;
                $order->paid_status = 'paid';
                $order->save();

                $existingPayment = Payment::where('transaction_id', $transactionId)->first();
                if (!$existingPayment) {
                    Payment::updateOrCreate([
                        'order_id' => $paypalPayment->order_id,
                        'payment_method' => 'paypal',
                        'amount' => $paypalPayment->amount,
                        'transaction_id' => $transactionId,
                    ]);
                }
                info('Payment updated', ['order' => $order]);

                return response()->json(['message' => 'Capture event processed successfully']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error handling capture event', 'error' => $e->getMessage()], 400);
            }
        }

        return response()->json(['message' => 'Event not processed']);
    }

    public function success(Request $request)
    {
        info('Success request', [
            'request' => $request->all(),
        ]);
        $token = $request->query('token'); // PayPal Order ID

        if (!$token) {
            return redirect()->route('home')->withErrors(['error' => 'Missing PayPal token.']);
        }
        $paymentGateway = PaymentGatewayCredential::first();
        $clientId = $paymentGateway->paypal_client_id;
        $secret = $paymentGateway->paypal_secret;
        $captureResponse = Http::withBasicAuth($clientId, $secret)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->send('POST', "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$token}/capture");


        if ($captureResponse->successful()) {
            $paypalData = $captureResponse->json();

            $amountInfo = $paypalData['purchase_units'][0]['payments']['captures'][0]['amount'] ?? [];
            $amount = $amountInfo['value'] ?? 0;
            $transactionId = $paypalData['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';
            $paypalPayment = PaypalPayment::where('paypal_payment_id', $token)->first();

            if ($paypalPayment) {
                $paypalPayment->payment_status = 'completed';
                $paypalPayment->save();
            }


            if ($paypalPayment->payment_status == 'completed') {
                Payment::updateOrCreate(
            [
                'order_id' => $paypalPayment->order_id,
                'payment_method' => 'due',
                'amount' => $paypalPayment->amount,
            ],
            [
                'payment_method' => 'paypal',
                'branch_id' => $order->branch_id ?? null,
                'transaction_id' => $transactionId,
            ]);

                $order = Order::find($paypalPayment->order_id);
                $order->amount_paid = $order->amount_paid + $paypalPayment->amount;
                $order->status = 'paid';
                $order->save();

                SendNewOrderReceived::dispatch($order);

                if ($order->customer_id) {
                    SendOrderBillEvent::dispatch($order);
                }
                return redirect()->route('order_success', $paypalPayment->order->uuid)->with([
                    'flash.banner' => __('messages.paymentDoneSuccessfully'),
                    'flash.bannerStyle' => 'success',
                ]);
            }
        }
    }
    public function cancel(Request $request)
    {
        $token = $request->query('token'); // PayPal Order ID

        $paypalPayment = PaypalPayment::where('paypal_payment_id', $token)->first();

        if ($paypalPayment) {
            $paypalPayment->payment_status = 'failed';
            $paypalPayment->save();
        }

        session()->flash('flash.banner',  'Payment was cancelled.');
        session()->flash('flash.bannerStyle', 'warning');



        return redirect()->route('order_success', $paypalPayment->order->uuid);
    }





}
