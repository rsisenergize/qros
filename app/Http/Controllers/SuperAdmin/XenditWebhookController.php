<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use App\Models\Restaurant;
use App\Models\RestaurantPayment;
use App\Models\SuperadminPaymentGateway;
use App\Models\User;
use App\Notifications\RestaurantUpdatedPlan;
use App\Models\EmailSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class XenditWebhookController extends Controller
{
    private $secretKey;

    /**
     * Set Xendit configuration based on environment
     */
    private function setXenditConfigs()
    {
        $paymentGateway = SuperadminPaymentGateway::first();

        if ($paymentGateway->xendit_mode == 'sandbox') {
            $this->secretKey = $paymentGateway->test_xendit_secret_key;
        } else {
            $this->secretKey = $paymentGateway->live_xendit_secret_key;
        }
    }

    /**
     * Handle Xendit webhook for subscription payments
     */
    public function handleSubscriptionWebhook(Request $request, $hash)
    {
        $this->setXenditConfigs();

        // Log the incoming webhook
        Log::info('Xendit Subscription Webhook Received:', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        $status = $request->status;
        $externalId = $request->external_id;
        $invoiceId = $request->id;
        $amount = $request->amount;
        // Get reference_id from either the root or nested data (for recurring webhooks)
        $referenceId = $request->reference_id ?? ($request->data['reference_id'] ?? null);

        Log::info('Xendit Webhook Processing:', [
            'status' => $status,
            'external_id' => $externalId,
            'invoice_id' => $invoiceId,
            'amount' => $amount,
            'reference_id' => $referenceId
        ]);

        if ($status === 'PAID') {
            return $this->handlePaidWebhook($externalId, $invoiceId, $amount, $referenceId);
        }

        if ($status === 'EXPIRED' || $status === 'FAILED') {
            return $this->handleFailedWebhook($externalId, $status, $referenceId);
        }

        // Handle Xendit recurring payment webhook (if applicable)
        $body = $request->all();

        if (
            isset($body['event'], $body['data']) &&
            $body['event'] === 'recurring.cycle.succeeded' &&
            ($data = $body['data']) &&
            isset($data['status'], $data['amount'], $data['plan_id']) &&
            $data['status'] === 'SUCCEEDED'
        ) {
            return $this->recurringPaymentSuccess($body);
        }

        Log::info('Xendit Webhook: Status not processed', ['status' => $status]);
        return response()->json(['message' => 'Status not processed']);
    }

    /**
     * Handle successful payment webhook
     */
    private function handlePaidWebhook($externalId, $invoiceId, $amount)
    {
        try {
            // Find subscription by external_id
            $subscription = GlobalSubscription::where('transaction_id', $externalId)->first();

            if (!$subscription) {
                Log::error('Xendit Webhook: Subscription not found', ['external_id' => $externalId]);
                return response()->json(['message' => 'Subscription not found'], 404);
            }

            Log::info('Xendit Webhook: Found subscription', [
                'subscription_id' => $subscription->id,
                'restaurant_id' => $subscription->restaurant_id,
                'package_id' => $subscription->package_id
            ]);

            // Check if subscription is already active
            if ($subscription->subscription_status === 'active') {
                Log::info('Xendit Webhook: Subscription already active', ['subscription_id' => $subscription->id]);
                return response()->json(['message' => 'Subscription already active']);
            }

            $paymentDateTime = now()->format('Y-m-d H:i:s');
            $nextPayDate = now()->{(($subscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d');

            // Deactivate other active subscriptions for this restaurant
            // Cancel previous Xendit subscriptions before deactivating in database
            $this->cancelPreviousXenditSubscriptions($subscription->restaurant_id);
            GlobalSubscription::where('restaurant_id', $subscription->restaurant_id)
                ->where('subscription_status', 'active')
                ->where('id', '!=', $subscription->id)
                ->update(['subscription_status' => 'inactive', 'ends_at' => now()]);

            // Activate current subscription
            $subscription->subscription_status = 'active';
            $subscription->subscribed_on_date = $paymentDateTime;
            $subscription->save();

            Log::info('Xendit Webhook: Next pay date', ['next_pay_date' => $nextPayDate]);

            $invoice = GlobalInvoice::updateOrCreate(
                [
                    'global_subscription_id' => $subscription->id,
                    'invoice_id' => $invoiceId,
                ],
                [
                    'restaurant_id' => $subscription->restaurant_id,
                    'package_id' => $subscription->package_id,
                    'currency_id' => $subscription->currency_id,
                    'pay_date' => now()->format('Y-m-d'),
                    'next_pay_date' => now()->{(($subscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d'),
                    'status' => 'active',
                    'package_type' => $subscription->package_type,
                    'gateway_name' => 'xendit',
                    'total' => $amount,
                    'amount' => $amount,
                    'transaction_id' => $invoiceId,  // Use invoice ID as unique transaction ID
                    'reference_id' => $externalId,   // Store external_id as reference
                    'plan_id' => $subscription->subscription_id,
                    'subscription_id' => $subscription->subscription_id,
                ]
            );



            // Update restaurant
            $restaurant = Restaurant::find($subscription->restaurant_id);
            if ($restaurant) {
                $restaurant->package_id = $subscription->package_id;
                $restaurant->package_type = $subscription->package_type;
                $restaurant->status = 'active';
                $restaurant->license_expire_on = null;
                $restaurant->save();

                Log::info('Xendit Webhook: Restaurant updated', [
                    'restaurant_id' => $restaurant->id,
                    'package_id' => $restaurant->package_id,
                    'package_type' => $restaurant->package_type
                ]);

                // Send notifications to super admin and restaurant admin
                $this->sendNotifications($restaurant, $subscription->package_id);
            }

            // Send notifications

            Log::info('Xendit Webhook: Payment processed successfully', [
                'subscription_id' => $subscription->id,
                'invoice_pk' => $invoice->id
            ]);

            return response()->json(['message' => 'Xendit subscription PAID processed successfully']);

        } catch (\Exception $e) {
            Log::error('Xendit Webhook: Error handling PAID', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error handling subscription PAID', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle recurring payment success
     */
    private function recurringPaymentSuccess($body)
    {
        info('trying to process recurring payment', $body);
        try {
            $data = $body['data'] ?? [];
            $referenceId = $data['reference_id'] ?? null; // our external_id used when creating plan
            $planId = $data['plan_id'] ?? null;           // Xendit plan ID
            $cycleId = $data['id'] ?? null;               // event/cycle/invoice id if present
            $amount = $data['amount'] ?? ($body['amount'] ?? null);

            // Try to locate subscription: prefer reference_id match on our transaction_id, else fallback to plan_id on subscription_id
            $subscription = null;
            if ($referenceId) {
                $subscription = GlobalSubscription::where('transaction_id', $referenceId)->first();
            }
            if (!$subscription && $planId) {
                $subscription = GlobalSubscription::where('subscription_id', $planId)->first();
            }

            Log::info('Xendit Recurring: Subscription found', ['subscription' => $subscription]);

            if (!$subscription) {
                Log::error('Xendit Recurring: Subscription not found', ['reference_id' => $referenceId, 'plan_id' => $planId]);
                return response()->json(['message' => 'Subscription not found'], 404);
            }
            // Check if this cycle payment has already been recorded (prevent duplicate processing)
            $existingInvoice = GlobalInvoice::where('invoice_id', $cycleId)->first();
            if ($existingInvoice) {
                Log::info('Xendit Recurring: Payment already processed', ['cycle_id' => $cycleId]);
                return response()->json(['message' => 'Payment already processed']);
            }

            // Extract action_id for traceability
            $actionId = $data['attempt_details'][0]['action_id'] ?? null;
            $cycleNumber = $data['cycle_number'] ?? null;

            // Use 'updated' timestamp (when payment succeeded) or fall back to 'created' or now()
            $paymentDateTime = isset($data['updated'])
                ? Carbon::parse($data['updated'])->format('Y-m-d H:i:s')
                : (isset($data['created']) ? Carbon::parse($data['created'])->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'));

            // Calculate next payment date based on subscription type
            $nextPayDate = Carbon::parse($paymentDateTime)
                ->{(($subscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()
                ->format('Y-m-d H:i:s');

            // Create new invoice for recurring payment
            $invoice = new GlobalInvoice();
            $invoice->restaurant_id = $subscription->restaurant_id;
            $invoice->package_id = $subscription->package_id;
            $invoice->currency_id = $subscription->currency_id;
            $invoice->global_subscription_id = $subscription->id;
            $invoice->pay_date = $paymentDateTime;
            $invoice->next_pay_date = $nextPayDate;
            $invoice->status = 'active';
            $invoice->package_type = $subscription->package_type;
            $invoice->gateway_name = 'xendit';
            $invoice->total = $amount;
            $invoice->amount = $amount;
            $invoice->plan_id = $planId;
            $invoice->subscription_id = $planId;
            $invoice->transaction_id = $cycleId;  // Unique transaction ID per cycle
            $invoice->reference_id = $referenceId;  // Subscription reference ID
            $invoice->invoice_id = $cycleId;
            $invoice->event_id = $actionId;  // Xendit action/payment ID
            $invoice->save();

            // Create RestaurantPayment record for recurring payment
            $restaurantPayment = new RestaurantPayment();
            $restaurantPayment->restaurant_id = $subscription->restaurant_id;
            $restaurantPayment->package_id = $subscription->package_id;
            $restaurantPayment->amount = $amount;
            $restaurantPayment->status = 'paid';
            $restaurantPayment->payment_date_time = $paymentDateTime;
            $restaurantPayment->transaction_id = $cycleId;  // Unique transaction ID per cycle
            $restaurantPayment->reference_id = $referenceId;  // Subscription reference ID
            $restaurantPayment->save();

            Log::info('Xendit Recurring: Payment processed', [
                'subscription_id' => $subscription->id,
                'invoice_pk' => $invoice->id,
                'restaurant_payment_pk' => $restaurantPayment->id,
                'amount' => $amount,
                'cycle_id' => $cycleId,
                'reference_id' => $referenceId,
                'action_id' => $actionId,
                'cycle_number' => $cycleNumber,
                'plan_id' => $planId
            ]);

            return response()->json(['message' => 'Recurring payment processed successfully']);
        } catch (\Exception $e) {
            Log::error('Xendit Recurring: Error processing payment', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error processing recurring payment'], 400);
        }
    }

    /**
     * Handle failed payment webhook
     */
    private function handleFailedWebhook($externalId, $status)
    {
        try {
            $subscription = GlobalSubscription::where('transaction_id', $externalId)->first();

            if ($subscription) {
                // Keep status within allowed enum [active, inactive]
                $subscription->subscription_status = 'inactive';
                $subscription->ends_at = now();
                $subscription->save();

                Log::info('Xendit Webhook: Subscription marked inactive due to failure', [
                    'subscription_id' => $subscription->id,
                    'status' => $status
                ]);
            }

            return response()->json(['message' => "Xendit subscription {$status} processed"]);

        } catch (\Exception $e) {
            Log::error('Xendit Webhook: Error handling failure', [
                'external_id' => $externalId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error handling subscription failure', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Send notifications for successful payment
     */
    private function sendNotifications($restaurant, $packageId)
    {
        try {
            $emailSetting = EmailSetting::first();

            if ($emailSetting && $emailSetting->mail_driver === 'smtp' && $emailSetting->verified) {
                // Notify super admin
                $generatedBy = User::withoutGlobalScopes()->whereNull('restaurant_id')->first();
                if ($generatedBy) {
                    Notification::send($generatedBy, new RestaurantUpdatedPlan($restaurant, $packageId));
                }

                // Notify restaurant admin
                $restaurantAdmin = $restaurant->restaurantAdmin($restaurant);
                if ($restaurantAdmin) {
                    Notification::send($restaurantAdmin, new RestaurantUpdatedPlan($restaurant, $packageId));
                }

                Log::info('Xendit Webhook: Notifications sent', [
                    'restaurant_id' => $restaurant->id,
                    'package_id' => $packageId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Xendit Webhook: Error sending notifications', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);
        }
    }



    /**
     * Handle successful recurring payment
     */


    /**
     * Handle failed recurring payment
     */


    /**
     * Cancel previous Xendit subscriptions for the restaurant
     */
    public function cancelPreviousXenditSubscriptions($restaurantId): void
    {
        try {
            // Find all active Xendit subscriptions for this restaurant
            $activeSubscriptions = GlobalSubscription::where('restaurant_id', $restaurantId)
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
                            Log::info('Xendit Webhook: Previous subscription cancelled', [
                                'plan_id' => $invoice->plan_id,
                                'subscription_id' => $subscription->id,
                                'restaurant_id' => $restaurantId
                            ]);
                        } else {
                            Log::warning('Xendit Webhook: Failed to cancel previous subscription', [
                                'plan_id' => $invoice->plan_id,
                                'subscription_id' => $subscription->id,
                                'response' => $response->json()
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Xendit Webhook: Error cancelling previous subscription', [
                            'plan_id' => $invoice->plan_id,
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Xendit Webhook: Error in cancelPreviousXenditSubscriptions', [
                'restaurant_id' => $restaurantId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
