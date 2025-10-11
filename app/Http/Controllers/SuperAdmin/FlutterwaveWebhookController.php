<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Models\Package;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use Illuminate\Routing\Controller;
use App\Models\SuperadminPaymentGateway;
use App\Models\RestaurantPayment;
use App\Notifications\RestaurantUpdatedPlan;
use Illuminate\Support\Facades\Notification;

class FlutterwaveWebhookController extends Controller
{
    public function handleWebhook(Request $request, $hash)
    {
        // Retrieve Flutterwave settings
        $settings = SuperadminPaymentGateway::first();

        if (!$settings || $hash !== global_setting()->hash) {
            return response()->json(['error' => true, 'message' => 'Unauthorized'], 403);
        }

        $signature = $request->header('verif-hash');

        // Verify Flutterwave signature
        if (!$signature) {
            return response()->json(['error' => true, 'message' => 'Invalid signature'], 403);
        }

        $payload = $request->all();

        // Extract event and data
        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];
        switch ($event) {
            case 'charge.completed':
                return $this->handlePaymentSuccess($data);

            case 'subscription.create':
            case 'subscription.cancel':
                return $this->handleSubscriptionEvent($data);

            default:
                return response()->json(['status' => 'success']);
        }
    }

    private function handlePaymentSuccess($data)
    {
        $transactionRef = $data['tx_ref'] ?? null;
        $transactionId = $data['tx_ref'] ?? null;
        $status = $data['status'] ?? 'failed';
        $amount = $data['amount'] ?? 0;

        $currency = $data['currency'] ?? null;
        $customerEmail = $data['customer']['email'] ?? null;

        if (!$transactionRef || $status !== 'successful') {
            return response()->json(['status' => 'error', 'message' => 'Payment failed'], 400);
        }

        // Find the restaurant associated with this payment
        $restaurantPayment = RestaurantPayment::where('flutterwave_payment_ref', $transactionRef)->first();

        if (!$restaurantPayment) {
            return response()->json(['status' => 'error', 'message' => 'Payment record not found'], 404);
        }

        $restaurant = Restaurant::find($restaurantPayment->restaurant_id);

        if (!$restaurant) {
            return response()->json(['status' => 'error', 'message' => 'Restaurant not found'], 404);
        }

        $package = Package::find($restaurantPayment->package_id);

        if (!$package) {
            return response()->json(['status' => 'error', 'message' => 'Package not found'], 404);
        }

        $restaurantPayment->flutterwave_transaction_id = $transactionId;
        $restaurantPayment->status = 'paid';
        $restaurantPayment->payment_date_time = now()->toDateTimeString();
        $restaurantPayment->save();

        $globalSubscription = GlobalSubscription::where('gateway_name', 'flutterwave')
            ->where('restaurant_id', $restaurant->id)
            ->latest()
            ->first();

        $existingInvoice = GlobalInvoice::where('transaction_id', $transactionId)->first();

        if (!$existingInvoice) {
            $invoice = new GlobalInvoice();
            $invoice->global_subscription_id = $globalSubscription->id;
            $invoice->restaurant_id = $restaurant->id;
            $invoice->invoice_id = $transactionRef;
            $invoice->transaction_id = $transactionId;
            $invoice->amount = $amount;
            $invoice->total = $amount;
            $invoice->currency_id = $globalSubscription->currency_id;
            $invoice->package_type = $globalSubscription->package_type;
            $invoice->package_id = $package->id;
            $invoice->pay_date = now();
            $invoice->gateway_name = 'flutterwave';
            $invoice->status = 'active';
            $invoice->plan_id = match ($globalSubscription->package_type) {
                'lifetime' => null,
                'annual' => $package->flutterwave_annual_plan_id,
                'monthly' => $package->flutterwave_monthly_plan_id,
                default => null
            };
            $invoice->save();
        }

        $restaurant->package_id = $package->id;
        $restaurant->status = 'active';
        $restaurant->is_active = true;
        $restaurant->license_expire_on = null;
        $restaurant->save();

        $adminUsers = User::whereNull('restaurant_id')->get();

        Notification::send($adminUsers, new RestaurantUpdatedPlan($restaurant, $package->id));

        return response()->json(['status' => 'success', 'message' => 'Payment processed successfully']);
    }

    private function handleSubscriptionEvent($data)
    {
        $subscriptionId = $data['id'] ?? null;
        $planId = $data['plan'] ?? null;
        $customerId = $data['customer']['id'] ?? null;
        $status = $data['status'] ?? 'inactive';

        if (!$subscriptionId) {
            return response()->json(['status' => 'error', 'message' => 'Missing subscription ID'], 400);
        }

        $restaurant = Restaurant::where('flutterwave_customer_id', $customerId)->first();
        if (!$restaurant) {
            return response()->json(['status' => 'error', 'message' => 'Restaurant not found'], 404);
        }

        $subscription = GlobalSubscription::where('subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            $subscription = new GlobalSubscription();
            $subscription->subscription_id = $subscriptionId;
            $subscription->restaurant_id = $restaurant->id;
            $subscription->flutterwave_customer_id = $customerId;
            $subscription->gateway_name = 'flutterwave';
        }

        $subscription->plan_id = $planId;
        $subscription->subscription_status = $status;
        $subscription->save();
        info('subscriptionUpdated');

        return response()->json(['status' => 'success', 'message' => 'Subscription updated']);
    }
}
