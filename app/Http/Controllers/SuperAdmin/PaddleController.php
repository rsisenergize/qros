<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use App\Models\Package;
use App\Models\Restaurant;
use App\Models\RestaurantPayment;
use App\Models\SuperadminPaymentGateway;
use App\Models\User;
use App\Models\EmailSetting;
use App\Notifications\RestaurantUpdatedPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PaddleController extends Controller
{
    private $apiKey;
    private $vendorId;
    private $clientToken;
    private $apiUrl;

    /**
     * Load Paddle API credentials from DB
     */
    private function setPaddleConfigs(): void
    {
        $gateway = SuperadminPaymentGateway::first();

        if (!$gateway) {
            throw new \Exception("No SuperadminPaymentGateway found");
        }

        $isSandbox = $gateway->paddle_mode === 'sandbox';

        $this->vendorId = $isSandbox
            ? $gateway->test_paddle_vendor_id
            : $gateway->live_paddle_vendor_id;

        $this->apiKey = $isSandbox
            ? $gateway->test_paddle_api_key
            : $gateway->live_paddle_api_key;

        $this->clientToken = $isSandbox
            ? $gateway->test_paddle_client_token
            : $gateway->live_paddle_client_token;

        $this->apiUrl = $isSandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        if (empty($this->apiKey)) {
            throw new \Exception("Paddle API key not configured");
        }
    }

    /**
     * Get or create Paddle customer
     */
    private function createCustomer(Restaurant $restaurant): string
    {
        $email = user()->email ?? $restaurant->email;

        // First, try to find existing customer by email
        $searchResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->get($this->apiUrl . '/customers', [
            'email' => $email
        ]);

        $searchData = $searchResponse->json();

        // If customer exists, return the first one
        if ($searchResponse->successful() && isset($searchData['data']) && count($searchData['data']) > 0) {
            $customerId = $searchData['data'][0]['id'];
            Log::info('Paddle: Existing customer found', [
                'customer_id' => $customerId,
                'email' => $email
            ]);
            return $customerId;
        }

        // If customer doesn't exist, create new one
        $payload = [
            'email' => $email,
            'name' => user()->name ?? $restaurant->name,
        ];

        Log::info('Paddle Customer Create Request', ['payload' => $payload, 'url' => $this->apiUrl . '/customers']);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl . '/customers', $payload);

        $data = $response->json();
        Log::info('Paddle Customer Response', [
            'status' => $response->status(),
            'data' => $data
        ]);

        if (!$response->successful() || !isset($data['data']['id'])) {
            // Check if error is due to duplicate email
            if (isset($data['error']['errors']) && is_array($data['error']['errors'])) {
                foreach ($data['error']['errors'] as $error) {
                    if (isset($error['field']) && $error['field'] === 'email' && isset($error['meta']['related_customer_id'])) {
                        // Use the related customer ID from error
                        Log::info('Paddle: Using existing customer from error', [
                            'customer_id' => $error['meta']['related_customer_id']
                        ]);
                        return $error['meta']['related_customer_id'];
                    }
                }
            }

            $errorDetail = $data['error']['detail'] ?? ($data['error_description'] ?? json_encode($data));
            Log::error('Paddle Customer Creation Failed', ['error' => $errorDetail, 'response' => $data]);
            throw new \Exception("Failed to create Paddle customer: " . $errorDetail);
        }

        return $data['data']['id'];
    }

    /**
     * Create or get Paddle product
     */
    private function createProduct(Package $package, string $type): string
    {
        // Create a product in Paddle
        $productName = $package->name . ' - ' . ucfirst($type);

        $payload = [
            'name' => $productName,
            'description' => ucfirst($type) . ' subscription for ' . $package->name,
            'tax_category' => 'standard', // Required field
        ];

        Log::info('Paddle Product Request', ['payload' => $payload, 'url' => $this->apiUrl . '/products']);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl . '/products', $payload);

        $data = $response->json();
        Log::info('Paddle Product Response', [
            'status' => $response->status(),
            'data' => $data
        ]);

        if (!$response->successful() || !isset($data['data']['id'])) {
            $errorDetail = $data['error']['detail'] ?? ($data['error_description'] ?? json_encode($data));
            Log::error('Paddle Product Creation Failed', ['error' => $errorDetail, 'response' => $data]);
            throw new \Exception("Failed to create Paddle product: " . $errorDetail);
        }

        return $data['data']['id'];
    }

    /**
     * Create Paddle price for product
     */
    private function createPrice(string $productId, Package $package, string $type, float $amount): string
    {
        $interval = $type === 'monthly' ? 'month' : ($type === 'annual' ? 'year' : null);

        $payload = [
            'description' => $package->name . ' - ' . ucfirst($type),
            'product_id' => $productId,
            'unit_price' => [
                'amount' => (string) round($amount * 100), // Paddle uses smallest currency unit (cents)
                'currency_code' => strtoupper($package->currency->currency_code),
            ],
        ];

        // Add billing cycle for recurring subscriptions
        if ($interval) {
            $payload['billing_cycle'] = [
                'interval' => $interval,
                'frequency' => 1,
            ];
        }

        Log::info('Paddle Price Request', ['payload' => $payload, 'url' => $this->apiUrl . '/prices']);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl . '/prices', $payload);

        $data = $response->json();
        Log::info('Paddle Price Response', [
            'status' => $response->status(),
            'data' => $data
        ]);

        if (!$response->successful() || !isset($data['data']['id'])) {
            $errorDetail = $data['error']['detail'] ?? ($data['error_description'] ?? json_encode($data));
            Log::error('Paddle Price Creation Failed', ['error' => $errorDetail, 'response' => $data]);
            throw new \Exception("Failed to create Paddle price: " . $errorDetail);
        }

        return $data['data']['id'];
    }

    /**
     * Initiate Paddle payment
     */
    public function initiatePaddlePayment(Request $request)
    {
        try {
            Log::info('Paddle Payment Initiation Started', [
                'request' => $request->all()
            ]);

            $this->setPaddleConfigs();

            $package = Package::findOrFail($request->package_id);
            $restaurant = restaurant();
            $type = $request->input('package_type');

            Log::info('Paddle Payment Details', [
                'package_id' => $package->id,
                'package_name' => $package->name,
                'restaurant_id' => $restaurant->id,
                'type' => $type,
                'currency' => $package->currency->currency_code ?? 'N/A'
            ]);

            // Determine amount
            $amount = $this->determineAmount($package, $type);

            Log::info('Paddle Payment Amount', ['amount' => $amount, 'type' => $type]);

            // Cancel previous Paddle subscriptions
            $this->cancelPreviousPaddleSubscriptions($restaurant);

            $transactionId = 'paddle_' . time() . '_' . $package->id;

            // Create customer
            $customerId = $this->createCustomer($restaurant);

            if ($type === 'lifetime') {
                // One-time payment for lifetime
                return $this->createOneTimeTransaction($restaurant, $package, $amount, $transactionId, $customerId);
            } else {
                // Recurring subscription (monthly/annual)
                return $this->createRecurringSubscription($restaurant, $package, $type, $amount, $transactionId, $customerId);
            }
        } catch (\Exception $e) {
            Log::error('Paddle Initiation Error', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with([
                'flash.banner' => 'Paddle payment initiation failed: ' . $e->getMessage(),
                'flash.bannerStyle' => 'danger'
            ]);
        }
    }

    /**
     * Create one-time transaction (lifetime)
     */
    private function createOneTimeTransaction(Restaurant $restaurant, Package $package, float $amount, string $transactionId, string $customerId)
    {
        // Get price ID from package
        $priceId = $package->paddle_lifetime_price_id;

        if (!$priceId) {
            throw new \Exception("Paddle Lifetime Price ID is not configured for this package. Please add it in package settings.");
        }

        // Create local subscription record FIRST
        $subscription = GlobalSubscription::create([
            'restaurant_id' => $restaurant->id,
            'package_id' => $package->id,
            'currency_id' => $package->currency_id,
            'package_type' => 'lifetime',
            'quantity' => 1,
            'gateway_name' => 'paddle',
            'subscription_status' => 'inactive',
            'subscribed_on_date' => now(),
            'transaction_id' => $transactionId,
            'customer_id' => $customerId,
            'subscription_id' => null, // Will be updated after payment
        ]);

        // Store data in session for Paddle.js checkout
        session([
            'subscription_id' => $subscription->id,
            'package_amount' => $amount,
            'paddle_customer_id' => $customerId,
            'paddle_price_id' => $priceId,
            'paddle_transaction_id' => $transactionId,
        ]);

        Log::info('Paddle: Lifetime payment setup completed', [
            'subscription_id' => $subscription->id,
            'price_id' => $priceId,
            'customer_id' => $customerId,
            'amount' => $amount
        ]);

        // Return to view with Paddle.js checkout
        return redirect()->route('paddle.checkout.page');
    }

    /**
     * Create recurring subscription (via transaction with recurring items)
     */
    private function createRecurringSubscription(Restaurant $restaurant, Package $package, string $type, float $amount, string $transactionId, string $customerId)
    {
        // Get price ID from package
        $priceId = $type === 'monthly' ? $package->paddle_monthly_price_id : $package->paddle_annual_price_id;

        if (!$priceId) {
            throw new \Exception("Paddle " . ucfirst($type) . " Price ID is not configured for this package. Please add it in package settings.");
        }

        // Create local subscription record FIRST
        $subscription = GlobalSubscription::create([
            'restaurant_id' => $restaurant->id,
            'package_id' => $package->id,
            'currency_id' => $package->currency_id,
            'package_type' => $type,
            'quantity' => 1,
            'gateway_name' => 'paddle',
            'subscription_status' => 'inactive',
            'subscribed_on_date' => now(),
            'transaction_id' => $transactionId,
            'customer_id' => $customerId,
            'subscription_id' => null, // Will be updated after payment
        ]);

        // Store data in session for Paddle.js checkout
        session([
            'subscription_id' => $subscription->id,
            'package_amount' => $amount,
            'paddle_customer_id' => $customerId,
            'paddle_price_id' => $priceId,
            'paddle_transaction_id' => $transactionId,
            'paddle_package_type' => $type,
        ]);

        Log::info('Paddle: Recurring subscription setup completed', [
            'subscription_id' => $subscription->id,
            'price_id' => $priceId,
            'customer_id' => $customerId,
            'type' => $type,
            'amount' => $amount
        ]);

        // Return to view with Paddle.js checkout
        return redirect()->route('paddle.checkout.page');
    }

    /**
     * Show Paddle checkout page (Paddle.js)
     */
    public function showCheckoutPage()
    {
        $this->setPaddleConfigs();

        $priceId = session('paddle_price_id');
        $customerId = session('paddle_customer_id');
        $subscriptionId = session('subscription_id');
        $amount = session('package_amount');

        if (!$priceId || !$customerId) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => 'Missing payment information. Please try again.',
                'flash.bannerStyle' => 'danger'
            ]);
        }

        $subscription = GlobalSubscription::find($subscriptionId);
        $package = $subscription ? Package::find($subscription->package_id) : null;

        return view('paddle.checkout', [
            'priceId' => $priceId,
            'customerId' => $customerId,
            'subscriptionId' => $subscriptionId,
            'amount' => $amount,
            'clientToken' => $this->clientToken,
            'package' => $package,
            'subscription' => $subscription,
            'isSandbox' => strpos($this->apiUrl, 'sandbox') !== false,
        ]);
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
     * Handle Paddle callback (from Paddle.js checkout)
     * Note: This just marks payment as in progress. Webhook handles the actual completion.
     */
    public function handleGatewayCallback(Request $request)
    {
        $this->setPaddleConfigs();

        $subscriptionId = session('subscription_id');
        $paddleTransactionId = $request->input('paddle_transaction_id');

        Log::info('Paddle Callback Received', [
            'request' => $request->all(),
            'session_subscription_id' => $subscriptionId,
            'paddle_transaction_id' => $paddleTransactionId
        ]);

        if (!$subscriptionId) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => 'Missing payment information',
                'flash.bannerStyle' => 'danger'
            ]);
        }

        $globalSubscription = GlobalSubscription::find($subscriptionId);

        if ($globalSubscription && $paddleTransactionId) {
            // Fetch transaction details from Paddle to get subscription_id
            try {
                $txnResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])->get($this->apiUrl . '/transactions/' . $paddleTransactionId);

                if ($txnResponse->successful()) {
                    $txnData = $txnResponse->json();
                    $paddleSubscriptionId = $txnData['data']['subscription_id'] ?? null;

                    // Update subscription with Paddle IDs
                    $globalSubscription->subscription_id = $paddleSubscriptionId;
                    $globalSubscription->save();

                    Log::info('Paddle Transaction Details from Callback', [
                        'paddle_transaction_id' => $paddleTransactionId,
                        'paddle_subscription_id' => $paddleSubscriptionId,
                        'status' => $txnData['data']['status'] ?? 'unknown',
                        'local_subscription_id' => $globalSubscription->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Paddle: Error fetching transaction details in callback', ['error' => $e->getMessage()]);
            }
        }

        // Clear session
        session()->forget(['subscription_id', 'package_amount', 'paddle_transaction_id', 'paddle_subscription_id', 'paddle_customer_id', 'paddle_price_id', 'paddle_package_type']);

        Log::info('Paddle Callback: Redirecting to dashboard', [
            'subscription_id' => $subscriptionId
        ]);

        // Redirect to dashboard - webhook will handle the actual activation
        request()->session()->flash('flash.banner', __('messages.planUpgraded'));
            request()->session()->flash('flash.bannerStyle', 'success');
            request()->session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));

            return redirect()->route('dashboard')->with('livewire', true);
    }

    /**
     * Handle payment failure
     */
    public function paymentFailed(Request $request)
    {
        Log::info('Paddle Payment Failed/Cancelled', [
            'request' => $request->all()
        ]);

        session()->forget(['subscription_id', 'package_amount', 'paddle_transaction_id', 'paddle_subscription_id', 'paddle_customer_id', 'paddle_price_id', 'paddle_package_type']);

        return redirect()->route('dashboard')->with([
            'flash.banner' => 'Payment was cancelled or failed.',
            'flash.bannerStyle' => 'danger'
        ]);
    }

    /**
     * Cancel previous Paddle subscriptions
     */
    private function cancelPreviousPaddleSubscriptions(Restaurant $restaurant): void
    {
        try {
            $activeSubscriptions = GlobalSubscription::where('restaurant_id', $restaurant->id)
                ->where('gateway_name', 'paddle')
                ->where('subscription_status', 'active')
                ->get();

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
                            Log::info('Paddle: Previous subscription cancelled', [
                                'subscription_id' => $subscription->id,
                                'paddle_subscription_id' => $subscription->subscription_id,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Paddle: Error cancelling previous subscription', [
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Paddle: Error in cancelPreviousPaddleSubscriptions', [
                'restaurant_id' => $restaurant->id,
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
            Log::error('Paddle: Error sending notifications', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

