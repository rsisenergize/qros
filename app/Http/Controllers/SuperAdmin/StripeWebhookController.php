<?php

namespace App\Http\Controllers\SuperAdmin;

use Carbon\Carbon;
use Stripe\Webhook;
use App\Models\User;
use App\Models\Package;
use Stripe\StripeClient;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use Illuminate\Routing\Controller;
use App\Models\SuperadminPaymentGateway;
use App\Notifications\RestaurantUpdatedPlan;
use Illuminate\Support\Facades\Notification;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     *
     * @param Request $request
     * @param string $hash
     * @return \Illuminate\Http\Response
     */
    public function verifyStripeWebhook(Request $request, $hash)
    {
        $settings = SuperadminPaymentGateway::first();
        $stripeWebhookKey = $settings->stripe_webhook_key;

        if (is_null($stripeWebhookKey)) {
            return response()->json([
                'error' => true,
                'message' => 'Webhook secret is not entered',
            ], 400);
        }

        $stripe = new StripeClient($settings->stripe_secret);
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            Webhook::constructEvent(
                $payload,
                $sigHeader,
                $stripeWebhookKey
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid Payload: ' . $e->getMessage(), 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature: ' . $e->getMessage(), 400);
        }

        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['data']['object']['object'])) {
            return response('Payload data not found', 200);
        }

        $customerId = $payload['data']['object']['customer'];
        $restaurant = Restaurant::where('stripe_id', $customerId)->first();

        if (!$restaurant) {
            return response('Customer not found', 200);
        }

        $objectType = $payload['data']['object']['object'];
        $eventType = $payload['type'];

        // Handle invoice events
        if ($objectType === 'invoice') {
            if ($eventType === 'invoice.payment_succeeded') {
                return $this->handleInvoicePaymentSucceeded($payload, $restaurant);
            }

            if ($eventType === 'invoice.payment_failed') {
                return $this->handleInvoicePaymentFailed($payload, $restaurant);
            }
        }

        // Handle payment intent events
        if ($objectType === 'payment_intent') {
            if ($eventType === 'payment_intent.succeeded') {
                return $this->handlePaymentIntentSucceeded($restaurant);
            }

            if ($eventType === 'payment_intent.payment_failed') {
                return $this->handlePaymentIntentFailed($payload, $restaurant);
            }
        }

        return response('Event not handled', 200);
    }

    /**
     * Handle successful invoice payment
     *
     * @param array $payload
     * @param Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    private function handleInvoicePaymentSucceeded($payload, $restaurant)
    {
        $planId = $payload['data']['object']['lines']['data'][0]['plan']['id'];
        $invoiceNumber = $payload['data']['object']['number'];
        $amount = $payload['data']['object']['amount_paid'];
        $transactionId = $payload['data']['object']['payment_intent'];
        $invoiceRealId = $payload['data']['object']['id'];

        $package = Package::where(function ($query) use ($planId) {
            $query->where('stripe_annual_plan_id', '=', $planId)
                ->orWhere('stripe_monthly_plan_id', '=', $planId);
        })->first();

        $paidAt = isset($payload['data']['object']['status_transitions']['paid_at'])
            ? Carbon::createFromTimestamp($payload['data']['object']['status_transitions']['paid_at'])->format('Y-m-d H:i:s')
            : null;

        $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')
            ->where('restaurant_id', $restaurant->id)
            ->latest()
            ->first();

        $stripeInvoiceData = GlobalInvoice::where('gateway_name', 'stripe')
            ->where('restaurant_id', $restaurant->id)
            ->where('transaction_id', $transactionId)
            ->first();

        $nextPayDate = $globalSubscription->package_type == 'monthly'
            ? Carbon::parse($paidAt)->addMonth()->format('Y-m-d H:i:s')
            : Carbon::parse($paidAt)->addYear()->format('Y-m-d H:i:s');

        if (is_null($stripeInvoiceData)) {
            // Store invoice details
            $stripeInvoice = new GlobalInvoice();
            $stripeInvoice->global_subscription_id = $globalSubscription->id;
            $stripeInvoice->restaurant_id = $restaurant->id;
            $stripeInvoice->invoice_id = $invoiceRealId;
            $stripeInvoice->transaction_id = $transactionId;
            $stripeInvoice->amount = $amount / 100;
            $stripeInvoice->total = $amount / 100;
            $stripeInvoice->currency_id = $package->currency_id;
            $stripeInvoice->package_type = $globalSubscription->package_type;
            $stripeInvoice->package_id = $package->id;
            $stripeInvoice->pay_date = $paidAt;
            $stripeInvoice->next_pay_date = $nextPayDate;
            $stripeInvoice->stripe_invoice_number = $invoiceNumber;
            $stripeInvoice->gateway_name = 'stripe';
            $stripeInvoice->status = 'active';
            $stripeInvoice->save();

            // Update restaurant status
            $restaurant->package_id = $package->id;
            $restaurant->package_type = $globalSubscription->package_type;
            $restaurant->license_expire_on = null;
            $restaurant->status = 'active';
            $restaurant->save();

            // Send notifications
            $generatedBy = User::whereNull('restaurant_id')->get();

            try {
                Notification::send($generatedBy, new RestaurantUpdatedPlan($restaurant, $package->id));
            } catch (\Exception $e) {
                \Log::error('Error sending notification: ' . $e->getMessage());
            }
        }

        return response('Webhook Handled', 200);
    }

    /**
     * Handle failed invoice payment
     *
     * @param array $payload
     * @param Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    private function handleInvoicePaymentFailed($payload, $restaurant)
    {
        $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if (isset($payload['data']['object']['current_period_end'])) {
            $periodEnd = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');

            $globalSubscription->ends_at = $periodEnd;
            $globalSubscription->save();

            $restaurant->license_expire_on = $periodEnd;
            $restaurant->save();

            return response('Restaurant subscription canceled', 200);
        }

        return response('Current period end not found', 200);
    }

    /**
     * Handle successful payment intent
     *
     * @param Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    private function handlePaymentIntentSucceeded($restaurant)
    {
        $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if ($globalSubscription) {
            $globalSubscription->stripe_status = 'active';
            $globalSubscription->save();
        }

        return response('Webhook Handled', 200);
    }

    /**
     * Handle failed payment intent
     *
     * @param array $payload
     * @param Restaurant $restaurant
     * @return \Illuminate\Http\Response
     */
    private function handlePaymentIntentFailed($payload, $restaurant)
    {
        $globalSubscription = GlobalSubscription::where('gateway_name', 'stripe')
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if (isset($payload['data']['object']['current_period_end'])) {
            $periodEnd = Carbon::createFromTimeStamp($payload['data']['object']['current_period_end'])->format('Y-m-d');

            $globalSubscription->ends_at = $periodEnd;
            $globalSubscription->save();

            $restaurant->license_expire_on = $periodEnd;
            $restaurant->save();
        }

        return response('Payment intent failed', 400);
    }
}
