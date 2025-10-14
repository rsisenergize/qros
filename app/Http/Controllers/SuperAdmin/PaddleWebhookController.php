<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use App\Models\Restaurant;
use App\Models\RestaurantPayment;
use App\Models\User;
use App\Notifications\RestaurantUpdatedPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PaddleWebhookController extends Controller
{
    private $apiKey;
    private $apiUrl;

    /**
     * Load Paddle configuration
     */
    private function setPaddleConfigs()
    {
        $paymentGateway = \App\Models\SuperadminPaymentGateway::first();

        $isSandbox = $paymentGateway->paddle_mode === 'sandbox';

        $this->apiKey = $isSandbox
            ? $paymentGateway->test_paddle_api_key
            : $paymentGateway->live_paddle_api_key;

        $this->apiUrl = $isSandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';
    }

    /**
     * Handle Paddle webhook
     */
    public function handleWebhook(Request $request, $hash)
    {
        $this->setPaddleConfigs();

        // Validate hash
        if ($hash !== global_setting()->hash) {
            Log::warning('Paddle Webhook: Invalid hash', ['provided_hash' => $hash]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify Paddle webhook signature
        if (!$this->verifyWebhookSignature($request)) {
            Log::error('Paddle Webhook: Signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Log the incoming webhook
        Log::info('Paddle Webhook Received:', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        $eventType = $request->input('event_type');
        $data = $request->input('data');

        Log::info('Paddle Webhook Processing:', [
            'event_type' => $eventType,
            'data' => $data
        ]);

        switch ($eventType) {
            case 'transaction.completed':
            case 'transaction.paid':  // Also handle transaction.paid
                return $this->handleTransactionCompleted($data);

            case 'transaction.payment_failed':
                return $this->handleTransactionFailed($data);

            case 'subscription.activated':
            case 'subscription.created':  // Also handle subscription.created
                return $this->handleSubscriptionActivated($data);

            case 'subscription.updated':
                return $this->handleSubscriptionUpdated($data);

            case 'subscription.canceled':
                return $this->handleSubscriptionCanceled($data);

            case 'subscription.payment_succeeded':
                return $this->handleSubscriptionPaymentSucceeded($data);

            default:
                Log::info('Paddle Webhook: Event type not processed', ['event_type' => $eventType]);
                return response()->json(['message' => 'Event type not processed']);
        }
    }

    /**
     * Handle transaction completed (one-time payments and recurring)
     */
    private function handleTransactionCompleted($data)
    {
        try {
            $transactionId = $data['id'] ?? null;
            $customData = $data['custom_data'] ?? [];
            $status = $data['status'] ?? null;
            $paddleSubscriptionId = $data['subscription_id'] ?? null;

            Log::info('Paddle Webhook: Processing transaction.completed', [
                'paddle_transaction_id' => $transactionId,
                'paddle_subscription_id' => $paddleSubscriptionId,
                'status' => $status,
                'custom_data' => $customData
            ]);

            if ($status !== 'completed') {
                return response()->json(['message' => 'Transaction not completed']);
            }

            // Find subscription by our local subscription_id from custom_data
            $subscription = null;
            if (isset($customData['subscription_id'])) {
                $subscription = GlobalSubscription::find($customData['subscription_id']);
            }

            // Fallback: Try to find by our transaction_id
            if (!$subscription && isset($customData['transaction_id'])) {
                $subscription = GlobalSubscription::where('transaction_id', $customData['transaction_id'])->first();
            }

            // Fallback: Try to find by Paddle subscription_id
            if (!$subscription && $paddleSubscriptionId) {
                $subscription = GlobalSubscription::where('subscription_id', $paddleSubscriptionId)->first();
            }

            if (!$subscription) {
                Log::error('Paddle Webhook: Subscription not found', [
                    'paddle_transaction_id' => $transactionId,
                    'custom_subscription_id' => $customData['subscription_id'] ?? null,
                    'custom_transaction_id' => $customData['transaction_id'] ?? null
                ]);
                return response()->json(['message' => 'Subscription not found'], 404);
            }

            // Check if invoice already exists for this transaction
            $existingInvoice = GlobalInvoice::where('transaction_id', $transactionId)->first();
            if ($existingInvoice) {
                Log::info('Paddle Webhook: Invoice already exists for this transaction', [
                    'transaction_id' => $transactionId,
                    'invoice_id' => $existingInvoice->id
                ]);
                return response()->json(['message' => 'Invoice already processed']);
            }

            // Update subscription with Paddle subscription_id if available
            if ($paddleSubscriptionId && !$subscription->subscription_id) {
                $subscription->subscription_id = $paddleSubscriptionId;
            }

            // Deactivate other active subscriptions (EXCLUDE the current one)
            $this->cancelPreviousPaddleSubscriptions($subscription->restaurant_id, $subscription->id);
            GlobalSubscription::where('restaurant_id', $subscription->restaurant_id)
                ->where('subscription_status', 'active')
                ->where('id', '!=', $subscription->id)
                ->update(['subscription_status' => 'inactive', 'ends_at' => now()]);

            // Activate current subscription
            $subscription->subscription_status = 'active';
            $subscription->subscribed_on_date = now();
            $subscription->save();

            // Get amount (Paddle uses smallest currency unit - cents)
            $amount = isset($data['details']['totals']['total'])
                ? $data['details']['totals']['total'] / 100
                : 0;

            // Create invoice with Paddle transaction_id
            $invoice = GlobalInvoice::create([
                'restaurant_id' => $subscription->restaurant_id,
                'package_id' => $subscription->package_id,
                'currency_id' => $subscription->currency_id,
                'global_subscription_id' => $subscription->id,
                'pay_date' => now()->format('Y-m-d'),
                'next_pay_date' => $subscription->package_type === 'lifetime'
                    ? null
                    : now()->{$subscription->package_type === 'monthly' ? 'addMonth' : 'addYear'}()->format('Y-m-d'),
                'status' => 'active',
                'package_type' => $subscription->package_type,
                'gateway_name' => 'paddle',
                'total' => $amount,
                'amount' => $amount,
                'transaction_id' => $transactionId,  // Paddle transaction ID
                'reference_id' => $customData['transaction_id'] ?? null,  // Our reference ID
                'subscription_id' => $paddleSubscriptionId,  // Paddle subscription ID
                'invoice_id' => $data['invoice_id'] ?? null,  // Paddle invoice ID
            ]);

            // Create restaurant payment record
            RestaurantPayment::create([
                'restaurant_id' => $subscription->restaurant_id,
                'package_id' => $subscription->package_id,
                'amount' => $amount,
                'status' => 'paid',
                'payment_date_time' => now(),
                'transaction_id' => $transactionId,  // Paddle transaction ID
                'reference_id' => $customData['transaction_id'] ?? null,  // Our reference ID
            ]);

            // Update restaurant
            $restaurant = Restaurant::find($subscription->restaurant_id);
            if ($restaurant) {
                $restaurant->package_id = $subscription->package_id;
                $restaurant->package_type = $subscription->package_type;
                $restaurant->status = 'active';
                $restaurant->license_expire_on = null;
                $restaurant->save();

                // Send notifications
                $this->sendNotifications($restaurant, $subscription->package_id);
            }

            Log::info('Paddle Webhook: Transaction completed processed', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id
            ]);

            return response()->json(['message' => 'Transaction completed processed successfully']);

        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error handling transaction completed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error handling transaction completed', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle subscription activated
     */
    private function handleSubscriptionActivated($data)
    {

        try {
            $paddleSubscriptionId = $data['id'] ?? null;
            $customData = $data['custom_data'] ?? [];

            Log::info('Paddle Webhook: Processing subscription.activated', [
                'paddle_subscription_id' => $paddleSubscriptionId,
                'custom_data' => $customData
            ]);

            // Find subscription by our local subscription_id from custom_data
            $subscription = null;
            if (isset($customData['subscription_id'])) {
                $subscription = GlobalSubscription::find($customData['subscription_id']);
            }

            // Fallback: Try to find by our transaction_id
            if (!$subscription && isset($customData['transaction_id'])) {
                $subscription = GlobalSubscription::where('transaction_id', $customData['transaction_id'])->first();
            }

            // Fallback: Try to find by Paddle subscription_id
            if (!$subscription && $paddleSubscriptionId) {
                $subscription = GlobalSubscription::where('subscription_id', $paddleSubscriptionId)->first();
            }

            if (!$subscription) {
                Log::error('Paddle Webhook: Subscription not found for activation', [
                    'paddle_subscription_id' => $paddleSubscriptionId,
                    'custom_subscription_id' => $customData['subscription_id'] ?? null
                ]);
                return response()->json(['message' => 'Subscription not found'], 404);
            }

            // Update subscription with Paddle subscription_id
            if ($paddleSubscriptionId) {
                $subscription->subscription_id = $paddleSubscriptionId;
            }

            $subscription->subscription_status = 'active';
            $subscription->subscribed_on_date = now();
            $subscription->save();

            Log::info('Paddle Webhook: Subscription activated', [
                'local_subscription_id' => $subscription->id,
                'paddle_subscription_id' => $paddleSubscriptionId
            ]);

            return response()->json(['message' => 'Subscription activated successfully']);

        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error handling subscription activated', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error handling subscription activated'], 400);
        }
    }

    /**
     * Handle subscription payment succeeded (recurring payments)
     */
    private function handleSubscriptionPaymentSucceeded($data)
    {
        try {
            $subscriptionId = $data['subscription_id'] ?? null;
            $transactionId = $data['id'] ?? null;

            $subscription = GlobalSubscription::where('subscription_id', $subscriptionId)->first();

            if (!$subscription) {
                Log::error('Paddle Webhook: Subscription not found for recurring payment', [
                    'subscription_id' => $subscriptionId
                ]);
                return response()->json(['message' => 'Subscription not found'], 404);
            }

            // Check if this payment has already been recorded
            $existingInvoice = GlobalInvoice::where('transaction_id', $transactionId)->first();
            if ($existingInvoice) {
                Log::info('Paddle Webhook: Payment already processed', ['transaction_id' => $transactionId]);
                return response()->json(['message' => 'Payment already processed']);
            }

            // Get amount
            $amount = isset($data['details']['totals']['total'])
                ? $data['details']['totals']['total'] / 100
                : 0;

            // Create new invoice for recurring payment
            $invoice = GlobalInvoice::create([
                'restaurant_id' => $subscription->restaurant_id,
                'package_id' => $subscription->package_id,
                'currency_id' => $subscription->currency_id,
                'global_subscription_id' => $subscription->id,
                'pay_date' => now()->format('Y-m-d'),
                'next_pay_date' => now()->{$subscription->package_type === 'monthly' ? 'addMonth' : 'addYear'}()->format('Y-m-d'),
                'status' => 'active',
                'package_type' => $subscription->package_type,
                'gateway_name' => 'paddle',
                'total' => $amount,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'subscription_id' => $subscriptionId,
            ]);

            // Create RestaurantPayment record
            RestaurantPayment::create([
                'restaurant_id' => $subscription->restaurant_id,
                'package_id' => $subscription->package_id,
                'amount' => $amount,
                'status' => 'paid',
                'payment_date_time' => now(),
                'transaction_id' => $transactionId,
                'reference_id' => $subscription->transaction_id,
            ]);

            Log::info('Paddle Webhook: Recurring payment processed', [
                'subscription_id' => $subscription->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount
            ]);

            return response()->json(['message' => 'Recurring payment processed successfully']);

        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error processing recurring payment', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error processing recurring payment'], 400);
        }
    }

    /**
     * Handle transaction failed
     */
    private function handleTransactionFailed($data)
    {
        try {
            $transactionId = $data['id'] ?? null;
            $customData = $data['custom_data'] ?? [];

            $subscription = GlobalSubscription::where('transaction_id', $customData['transaction_id'] ?? null)
                ->orWhere('subscription_id', $transactionId)
                ->first();

            if ($subscription) {
                $subscription->subscription_status = 'inactive';
                $subscription->ends_at = now();
                $subscription->save();

                Log::info('Paddle Webhook: Transaction failed, subscription marked inactive', [
                    'subscription_id' => $subscription->id
                ]);
            }

            return response()->json(['message' => 'Transaction failed processed']);

        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error handling transaction failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error handling transaction failed'], 400);
        }
    }

    /**
     * Handle subscription canceled
     */
    private function handleSubscriptionCanceled($data)
    {
        try {
            $subscriptionId = $data['id'] ?? null;

            $subscription = GlobalSubscription::where('subscription_id', $subscriptionId)->first();

            if ($subscription) {
                $subscription->subscription_status = 'inactive';
                $subscription->ends_at = now();
                $subscription->save();

                Log::info('Paddle Webhook: Subscription canceled', [
                    'subscription_id' => $subscription->id
                ]);
            }

            return response()->json(['message' => 'Subscription canceled processed']);

        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error handling subscription canceled', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error handling subscription canceled'], 400);
        }
    }

    /**
     * Handle subscription updated
     */
    private function handleSubscriptionUpdated($data)
    {
        try {
            $subscriptionId = $data['id'] ?? null;

            $subscription = GlobalSubscription::where('subscription_id', $subscriptionId)->first();

            if ($subscription) {
                // Update subscription details if needed
                Log::info('Paddle Webhook: Subscription updated', [
                    'subscription_id' => $subscription->id,
                    'data' => $data
                ]);
            }

            return response()->json(['message' => 'Subscription updated processed']);

        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error handling subscription updated', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error handling subscription updated'], 400);
        }
    }

    /**
     * Verify Paddle webhook signature
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('Paddle-Signature');

        if (!$signature) {
            Log::error('Paddle Webhook: Missing signature header');
            return false;
        }

        // Parse signature header
        $signatureParts = [];
        foreach (explode(';', $signature) as $part) {
            $keyValue = explode('=', $part, 2);
            if (count($keyValue) === 2) {
                $signatureParts[trim($keyValue[0])] = trim($keyValue[1]);
            }
        }

        $timestamp = $signatureParts['ts'] ?? null;
        $receivedSignature = $signatureParts['h1'] ?? null;

        if (!$timestamp || !$receivedSignature) {
            Log::error('Paddle Webhook: Invalid signature format');
            return false;
        }

        // Check timestamp is within 5 minutes to prevent replay attacks
        $currentTime = time();
        if (abs($currentTime - $timestamp) > 300) {
            Log::error('Paddle Webhook: Timestamp too old', [
                'webhook_timestamp' => $timestamp,
                'current_time' => $currentTime,
                'difference' => abs($currentTime - $timestamp)
            ]);
            return false;
        }

        // Build signature payload
        $payload = $timestamp . ':' . $request->getContent();

        // Calculate expected signature using webhook secret
        // Note: Paddle webhook secret should be stored in payment gateway settings
        $paymentGateway = \App\Models\SuperadminPaymentGateway::first();
        $webhookSecret = $paymentGateway->paddle_webhook_secret ?? null;

        if (!$webhookSecret) {
            Log::warning('Paddle Webhook: Webhook secret not configured - skipping signature verification');
            // Allow webhook but log warning for backward compatibility
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        // Compare signatures
        if (!hash_equals($expectedSignature, $receivedSignature)) {
            Log::error('Paddle Webhook: Signature mismatch', [
                'expected' => $expectedSignature,
                'received' => $receivedSignature
            ]);
            return false;
        }

        return true;
    }

    /**
     * Cancel previous Paddle subscriptions
     */
    private function cancelPreviousPaddleSubscriptions($restaurantId, $excludeSubscriptionId = null): void
    {
        try {
            $query = GlobalSubscription::where('restaurant_id', $restaurantId)
                ->where('gateway_name', 'paddle')
                ->where('subscription_status', 'active');

            // Exclude the current subscription
            if ($excludeSubscriptionId) {
                $query->where('id', '!=', $excludeSubscriptionId);
            }

            $activeSubscriptions = $query->get();

            foreach ($activeSubscriptions as $subscription) {
                if ($subscription->subscription_id) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $this->apiKey,
                            'Content-Type' => 'application/json'
                        ])->post($this->apiUrl . '/subscriptions/' . $subscription->subscription_id . '/cancel', [
                            'effective_from' => 'immediately'
                        ]);

                        if ($response->successful()) {
                            Log::info('Paddle Webhook: Previous subscription cancelled', [
                                'subscription_id' => $subscription->id,
                                'paddle_subscription_id' => $subscription->subscription_id,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Paddle Webhook: Error cancelling previous subscription', [
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error in cancelPreviousPaddleSubscriptions', [
                'restaurant_id' => $restaurantId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notifications
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
            }
        } catch (\Exception $e) {
            Log::error('Paddle Webhook: Error sending notifications', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}



