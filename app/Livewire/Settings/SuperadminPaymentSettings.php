<?php

namespace App\Livewire\Settings;

use App\Models\SuperadminPaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SuperadminPaymentSettings extends Component
{

    use LivewireAlert;

    public $razorpaySecret;
    public $razorpayKey;
    public $testRazorpaySecret;
    public $testRazorpayKey;
    public $razorpayStatus;
    public $paymentGateway;
    public $stripeSecret;
    public $stripeKey;
    public $testStripeSecret;
    public $testStripeKey;
    public $stripeStatus;
    public $selectRazorpayEnvironment;
    public $selectStripeEnvironment;
    public $activePaymentSetting = 'razorpay';
    public $razorpayWebhookKey;
    public $testRazorpayWebhookKey;
    public $stripeWebhookKey;
    public $testStripeWebhookKey;
    public $webhookUrl;
    public $flutterwaveSecret;
    public $flutterwaveKey;
    public $flutterwaveHash;
    public $testFlutterwaveSecret;
    public $testFlutterwaveKey;
    public $testFlutterwaveHash;
    public $flutterwaveStatus;
    public $selectFlutterwaveEnvironment;
    public $flutterwaveWebhookKey;
    public $testFlutterwaveWebhookKey;
    public $paypalClientId;
    public $paypalSecret;
    public $testPaypalClientId;
    public $testPaypalSecret;
    public $paypalStatus;
    public $selectPaypalEnvironment;
    public $payfastMerchantId;
    public $payfastMerchantKey;
    public $payfastPassphrase;
    public $testPayfastMerchantId;
    public $testPayfastMerchantKey;
    public $testPayfastPassphrase;
    public $payfastStatus;
    public $selectPayfastEnvironment;
    public $paystackKey;
    public $paystackSecret;
    public $testPaystackKey;
    public $testPaystackSecret;
    public $paystackStatus;
    public $paystackMerchantEmail;
    public $testPaystackMerchantEmail;
    public $selectPaystackEnvironment;

    // Xendit properties (superadmin)
    public $xenditStatus;
    public $xenditMode; // sandbox|live
    public $testXenditPublicKey;
    public $testXenditSecretKey;
    public $liveXenditPublicKey;
    public $liveXenditSecretKey;
    public $testXenditWebhookToken;
    public $liveXenditWebhookToken;
    public $selectXenditEnvironment;
    public $isXenditEnabled;

    // Paddle properties
    public $paddleStatus;
    public $paddleMode;
    public $testPaddleVendorId;
    public $testPaddleApiKey;
    public $testPaddlePublicKey;
    public $testPaddleClientToken;
    public $livePaddleVendorId;
    public $livePaddleApiKey;
    public $livePaddlePublicKey;
    public $livePaddleClientToken;
    public $paddleWebhookSecret;
    public $isPaddleEnabled;


    public function mount()
    {
        $this->paymentGateway = SuperadminPaymentGateway::first();
        $this->setCredentials();
    }

    public function setCredentials()
    {
        $this->selectRazorpayEnvironment = $this->paymentGateway->razorpay_type;
        $this->razorpayStatus = (bool)$this->paymentGateway->razorpay_status;

        $this->razorpayKey = $this->paymentGateway->live_razorpay_key;
        $this->razorpaySecret = $this->paymentGateway->live_razorpay_secret;

        $this->testRazorpayKey = $this->paymentGateway->test_razorpay_key;
        $this->testRazorpaySecret = $this->paymentGateway->test_razorpay_secret;

        $this->selectStripeEnvironment = $this->paymentGateway->stripe_type;
        $this->stripeStatus = (bool)$this->paymentGateway->stripe_status;

        $this->stripeKey = $this->paymentGateway->live_stripe_key;
        $this->stripeSecret = $this->paymentGateway->live_stripe_secret;

        $this->testStripeKey = $this->paymentGateway->test_stripe_key;
        $this->testStripeSecret = $this->paymentGateway->test_stripe_secret;


        $this->razorpayWebhookKey = $this->paymentGateway->razorpay_live_webhook_key;
        $this->testRazorpayWebhookKey = $this->paymentGateway->razorpay_test_webhook_key;

        $this->stripeWebhookKey = $this->paymentGateway->stripe_live_webhook_key;
        $this->testStripeWebhookKey = $this->paymentGateway->stripe_test_webhook_key;

        $this->selectFlutterwaveEnvironment = $this->paymentGateway->flutterwave_type;
        $this->flutterwaveStatus = (bool)$this->paymentGateway->flutterwave_status;

        $this->flutterwaveKey = $this->paymentGateway->live_flutterwave_key;
        $this->flutterwaveSecret = $this->paymentGateway->live_flutterwave_secret;
        $this->flutterwaveHash = $this->paymentGateway->live_flutterwave_hash;

        $this->testFlutterwaveKey = $this->paymentGateway->test_flutterwave_key;
        $this->testFlutterwaveSecret = $this->paymentGateway->test_flutterwave_secret;
        $this->testFlutterwaveHash = $this->paymentGateway->test_flutterwave_hash;

        $hash = global_setting()->hash;
        $this->flutterwaveWebhookKey = $this->paymentGateway->flutterwave_live_webhook_key ? $this->paymentGateway->flutterwave_live_webhook_key : substr(md5($hash), 0, 10);
        $this->testFlutterwaveWebhookKey = $this->paymentGateway->flutterwave_test_webhook_key ? $this->paymentGateway->flutterwave_test_webhook_key : substr(md5($hash), 0, 10);

        $this->paypalClientId = $this->paymentGateway->live_paypal_client_id;
        $this->paypalSecret = $this->paymentGateway->live_paypal_secret;
        $this->testPaypalClientId = $this->paymentGateway->test_paypal_client_id;
        $this->testPaypalSecret = $this->paymentGateway->test_paypal_secret;
        $this->paypalStatus = (bool)$this->paymentGateway->paypal_status;
        $this->selectPaypalEnvironment = $this->paymentGateway->paypal_mode;

        $this->payfastMerchantId = $this->paymentGateway->live_payfast_merchant_id;
        $this->payfastMerchantKey = $this->paymentGateway->live_payfast_merchant_key;
        $this->payfastPassphrase = $this->paymentGateway->live_payfast_passphrase;
        $this->testPayfastMerchantId = $this->paymentGateway->test_payfast_merchant_id;
        $this->testPayfastMerchantKey = $this->paymentGateway->test_payfast_merchant_key;
        $this->testPayfastPassphrase = $this->paymentGateway->test_payfast_passphrase;
        $this->payfastStatus = (bool)$this->paymentGateway->payfast_status;
        $this->selectPayfastEnvironment = $this->paymentGateway->payfast_mode;

        $this->selectPaystackEnvironment = $this->paymentGateway->paystack_mode;
        $this->paystackStatus = (bool)$this->paymentGateway->paystack_status;
        $this->paystackKey = $this->paymentGateway->live_paystack_key;
        $this->paystackSecret = $this->paymentGateway->live_paystack_secret;
        $this->testPaystackKey = $this->paymentGateway->test_paystack_key;
        $this->testPaystackSecret = $this->paymentGateway->test_paystack_secret;
        $this->paystackMerchantEmail = $this->paymentGateway->live_paystack_merchant_email;
        $this->testPaystackMerchantEmail = $this->paymentGateway->test_paystack_merchant_email;

        $this->paddleStatus = (bool) ($this->paymentGateway->paddle_status ?? false);
        $this->paddleMode = $this->paymentGateway->paddle_mode ?? 'sandbox';
        $this->testPaddleVendorId = $this->paymentGateway->test_paddle_vendor_id ?? null;
        $this->testPaddleApiKey = $this->paymentGateway->test_paddle_api_key ?? null;
        $this->testPaddlePublicKey = $this->paymentGateway->test_paddle_public_key ?? null;
        $this->testPaddleClientToken = $this->paymentGateway->test_paddle_client_token ?? null;
        $this->livePaddleVendorId = $this->paymentGateway->live_paddle_vendor_id ?? null;
        $this->livePaddleApiKey = $this->paymentGateway->live_paddle_api_key ?? null;
        $this->livePaddlePublicKey = $this->paymentGateway->live_paddle_public_key ?? null;
        $this->livePaddleClientToken = $this->paymentGateway->live_paddle_client_token ?? null;
        $this->paddleWebhookSecret = $this->paymentGateway->paddle_webhook_secret ?? null;


        // Xendit credentials
        $this->xenditStatus = (bool)$this->paymentGateway->xendit_status;
        $this->xenditMode = $this->paymentGateway->xendit_mode; // sandbox|live
        $this->testXenditPublicKey = $this->paymentGateway->test_xendit_public_key;
        $this->testXenditSecretKey = $this->paymentGateway->test_xendit_secret_key;
        $this->liveXenditPublicKey = $this->paymentGateway->live_xendit_public_key;
        $this->liveXenditSecretKey = $this->paymentGateway->live_xendit_secret_key;
        $this->testXenditWebhookToken = $this->paymentGateway->test_xendit_webhook_token;
        $this->liveXenditWebhookToken = $this->paymentGateway->live_xendit_webhook_token;
        $this->isXenditEnabled = $this->paymentGateway->xendit_status;

        if ($this->activePaymentSetting === 'stripe') {
            $this->webhookUrl = route('billing.verify-webhook', ['hash' => $hash]);
        }

        if ($this->activePaymentSetting === 'razorpay') {
            $this->webhookUrl = route('billing.save_razorpay-webhook', ['hash' => $hash]);
        }

        if ($this->activePaymentSetting === 'flutterwave') {
            $this->webhookUrl = route('billing.save-flutterwave-webhook', ['hash' => $hash]);
        }

        if ($this->activePaymentSetting === 'paypal') {
            $hash = global_setting()->hash;
            $this->webhookUrl = route('billing.save_paypal-webhook', ['hash' => $hash]);
        }

        if ($this->activePaymentSetting === 'paystack') {
            $hash = global_setting()->hash;
            $this->webhookUrl = route('billing.save-paystack-webhook', ['hash' => $hash]);
        }

        if ($this->activePaymentSetting === 'xendit') {
            $hash = global_setting()->hash;
            $this->webhookUrl = route('billing.save-xendit-webhook', ['hash' => $hash]);
        }

        if ($this->activePaymentSetting === 'paddle') {
            $hash = global_setting()->hash;
            $this->webhookUrl = route('billing.save-paddle-webhook', ['hash' => $hash]);
        }

    }

    public function activeSetting($tab)
    {
        $this->activePaymentSetting = $tab;
        $this->setCredentials();
    }

    public function submitFormRazorpay()
    {
        $this->validate([
            'razorpaySecret' => Rule::requiredIf($this->razorpayStatus == true && $this->selectRazorpayEnvironment == 'live'),
            'razorpayKey' => Rule::requiredIf($this->razorpayStatus == true && $this->selectRazorpayEnvironment == 'live'),
            'testRazorpaySecret' => Rule::requiredIf($this->razorpayStatus == true && $this->selectRazorpayEnvironment == 'test'),
            'testRazorpayKey' => Rule::requiredIf($this->razorpayStatus == true && $this->selectRazorpayEnvironment == 'test'),
        ]);

        $configError = 0;

        // Set Razorpay credentials
        $razorKey = $this->selectRazorpayEnvironment == 'live' ? $this->razorpayKey : $this->testRazorpayKey;
        $razorSecret = $this->selectRazorpayEnvironment == 'live' ? $this->razorpaySecret : $this->testRazorpaySecret;

        // Test Razorpay credentials
        if ($this->razorpayStatus) {
            try {
                $response = Http::withBasicAuth($razorKey, $razorSecret)
                    ->get('https://api.razorpay.com/v1/contacts');

                if ($response->successful()) {
                    $this->paymentGateway->update([
                        'razorpay_type' => $this->selectRazorpayEnvironment,
                        'live_razorpay_key' => $this->razorpayKey,
                        'live_razorpay_secret' => $this->razorpaySecret,
                        'test_razorpay_key' => $this->testRazorpayKey,
                        'test_razorpay_secret' => $this->testRazorpaySecret,
                        'razorpay_live_webhook_key' => $this->razorpayWebhookKey,
                        'razorpay_test_webhook_key' => $this->testRazorpayWebhookKey,
                    ]);
                } else {
                    $configError = 1;
                    $this->addError('razorpayKey', 'Invalid Razorpay key or secret.');
                    $this->addError('testRazorpayKey', 'Invalid Razorpay key or secret.');
                }
            } catch (\Exception $e) {
                $this->addError('razorpayKey', 'Invalid Razorpay key or secret.');
                $this->addError('testRazorpayKey', 'Invalid Razorpay key or secret.');
            }
        }

        $this->paymentGateway->update([
            'razorpay_status' => $this->razorpayStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    // Stripe Form Submission
    public function submitFormStripe()
    {
        $this->validate([
            'stripeSecret' => Rule::requiredIf($this->stripeStatus == true && $this->selectStripeEnvironment == 'live'),
            'stripeKey' => Rule::requiredIf($this->stripeStatus == true && $this->selectStripeEnvironment == 'live'),
            'testStripeSecret' => Rule::requiredIf($this->stripeStatus == true && $this->selectStripeEnvironment == 'test'),
            'testStripeKey' => Rule::requiredIf($this->stripeStatus == true && $this->selectStripeEnvironment == 'test'),
        ]);

        $configError = 0;

        // Set Stripe credentials
        $stripeKey = $this->selectStripeEnvironment == 'live' ? $this->stripeKey : $this->testStripeKey;
        $stripeSecret = $this->selectStripeEnvironment == 'live' ? $this->stripeSecret : $this->testStripeSecret;

        // Test Stripe credentials
        if ($this->stripeStatus) {
            try {
                $response = Http::withToken($stripeSecret)
                    ->get('https://api.stripe.com/v1/customers');

                if ($response->successful()) {
                    $this->paymentGateway->update([
                        'live_stripe_key' => $this->stripeKey,
                        'live_stripe_secret' => $this->stripeSecret,
                        'test_stripe_key' => $this->testStripeKey,
                        'test_stripe_secret' => $this->testStripeSecret,
                        'stripe_type' => $this->selectStripeEnvironment,
                        'stripe_live_webhook_key' => $this->stripeWebhookKey,
                        'stripe_test_webhook_key' => $this->testStripeWebhookKey,
                    ]);
                } else {
                    $configError = 1;
                    $this->addError('stripeKey', 'Invalid Stripe key or secret.');
                    $this->addError('testStripeKey', 'Invalid Stripe key or secret.');
                }
            } catch (\Exception $e) {
                $this->addError('stripeKey', 'Invalid Stripe key or secret.');
                $this->addError('testStripeKey', 'Invalid Stripe key or secret.');
            }
        }

        $this->paymentGateway->update([
            'stripe_status' => $this->stripeStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }


    public function submitFormFlutterwave()
    {
        $this->validate([
            'flutterwaveSecret' => Rule::requiredIf($this->flutterwaveStatus == true && $this->selectFlutterwaveEnvironment == 'live'),
            'flutterwaveKey' => Rule::requiredIf($this->flutterwaveStatus == true && $this->selectFlutterwaveEnvironment == 'live'),
            'testFlutterwaveSecret' => Rule::requiredIf($this->flutterwaveStatus == true && $this->selectFlutterwaveEnvironment == 'test'),
            'testFlutterwaveKey' => Rule::requiredIf($this->flutterwaveStatus == true && $this->selectFlutterwaveEnvironment == 'test'),
        ]);

        $configError = 0;

        // Set Flutterwave credentials
        $flutterwaveKey = $this->selectFlutterwaveEnvironment == 'live' ? $this->flutterwaveKey : $this->testFlutterwaveKey;
        $flutterwaveSecret = $this->selectFlutterwaveEnvironment == 'live' ? $this->flutterwaveSecret : $this->testFlutterwaveSecret;

        // Test Flutterwave credentials
        if ($this->flutterwaveStatus) {
            try {
                $response = Http::withToken($flutterwaveSecret)
            ->get('https://api.flutterwave.com/v3/transactions');

                if ($response->successful()) {
                    $this->paymentGateway->update([
                        'flutterwave_type' => $this->selectFlutterwaveEnvironment,
                        'live_flutterwave_key' => $this->flutterwaveKey,
                        'live_flutterwave_secret' => $this->flutterwaveSecret,
                        'live_flutterwave_hash' => $this->flutterwaveHash,
                        'test_flutterwave_key' => $this->testFlutterwaveKey,
                        'test_flutterwave_secret' => $this->testFlutterwaveSecret,
                        'test_flutterwave_hash' => $this->testFlutterwaveHash,
                        'flutterwave_live_webhook_key' => $this->flutterwaveWebhookKey,
                        'flutterwave_test_webhook_key' => $this->testFlutterwaveWebhookKey,
                    ]);
                } else {
                    $configError = 1;
                    $this->addError('flutterwaveKey', 'Invalid Flutterwave key or secret.');
                    $this->addError('testFlutterwaveKey', 'Invalid Flutterwave key or secret.');
                }
            } catch (\Exception $e) {
                $this->addError('flutterwaveKey', 'Invalid Flutterwave key or secret.');
                $this->addError('testFlutterwaveKey', 'Invalid Flutterwave key or secret.');
            }
        }

        $this->paymentGateway->update([
            'flutterwave_status' => $this->flutterwaveStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    public function submitFormPaypal()
    {
        $this->validate([
            'paypalClientId' => Rule::requiredIf($this->paypalStatus == true && $this->selectPaypalEnvironment == 'live'),
            'paypalSecret' => Rule::requiredIf($this->paypalStatus == true && $this->selectPaypalEnvironment == 'live'),
            'testPaypalClientId' => Rule::requiredIf($this->paypalStatus == true && $this->selectPaypalEnvironment == 'sandbox'),
            'testPaypalSecret' => Rule::requiredIf($this->paypalStatus == true && $this->selectPaypalEnvironment == 'sandbox'),
        ]);

        $configError = 0;

        $paypalClientId = $this->selectPaypalEnvironment == 'live' ? $this->paypalClientId : $this->testPaypalClientId;
        $paypalSecret = $this->selectPaypalEnvironment == 'live' ? $this->paypalSecret : $this->testPaypalSecret;

        if ($this->paypalStatus) {
            $paypalBaseUrl = $this->selectPaypalEnvironment == 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            try {
                $response = Http::asForm()
                    ->withBasicAuth($paypalClientId, $paypalSecret)
                    ->post("{$paypalBaseUrl}/v1/oauth2/token", [
                        'grant_type' => 'client_credentials'
                    ]);
                if ($response->successful()) {
                    $this->paymentGateway->update([
                        'live_paypal_client_id' => $this->paypalClientId,
                        'live_paypal_secret' => $this->paypalSecret,
                        'test_paypal_client_id' => $this->testPaypalClientId,
                        'test_paypal_secret' => $this->testPaypalSecret,
                        'paypal_mode' => $this->selectPaypalEnvironment,
                    ]);
                } else {
                    $configError = 1;
                    $this->addError('paypalClientId', 'Invalid Paypal client id or secret.');
                    $this->addError('testPaypalClientId', 'Invalid Paypal client id or secret.');
                }
            } catch (\Exception $e) {
                $this->addError('paypalClientId', 'Invalid Paypal client id or secret.');
                $this->addError('testPaypalClientId', 'Invalid Paypal client id or secret.');
            }
        }

        $this->paymentGateway->update([
            'paypal_status' => $this->paypalStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    public function submitFormPayfast()
    {
        $this->validate([
            'payfastMerchantId' => Rule::requiredIf($this->payfastStatus == true && $this->selectPayfastEnvironment == 'live'),
            'payfastMerchantKey' => Rule::requiredIf($this->payfastStatus == true && $this->selectPayfastEnvironment == 'live'),
            'payfastPassphrase' => Rule::requiredIf($this->payfastStatus == true && $this->selectPayfastEnvironment == 'live'),
            'testPayfastMerchantId' => Rule::requiredIf($this->payfastStatus == true && $this->selectPayfastEnvironment == 'sandbox'),
            'testPayfastMerchantKey' => Rule::requiredIf($this->payfastStatus == true && $this->selectPayfastEnvironment == 'sandbox'),
            'testPayfastPassphrase' => Rule::requiredIf($this->payfastStatus == true && $this->selectPayfastEnvironment == 'sandbox'),
        ]);

        $configError = 0;

        $merchantId = $this->selectPayfastEnvironment == 'live' ? $this->payfastMerchantId : $this->testPayfastMerchantId;
        $merchantKey = $this->selectPayfastEnvironment == 'live' ? $this->payfastMerchantKey : $this->testPayfastMerchantKey;

        if ($this->payfastStatus) {
            if (empty($merchantId) || empty($merchantKey)) {
                $configError = 1;
                $this->addError('payfastMerchantId', 'Invalid Payfast merchant ID or key.');
                $this->addError('testPayfastMerchantId', 'Invalid Payfast merchant ID or key.');
            }
        }

        // Save configuration
        $this->paymentGateway->update([
            'live_payfast_merchant_id' => $this->payfastMerchantId,
            'live_payfast_merchant_key' => $this->payfastMerchantKey,
            'live_payfast_passphrase' => $this->payfastPassphrase,
            'test_payfast_merchant_id' => $this->testPayfastMerchantId,
            'test_payfast_merchant_key' => $this->testPayfastMerchantKey,
            'test_payfast_passphrase' => $this->testPayfastPassphrase,
            'payfast_mode' => $this->selectPayfastEnvironment,
            'payfast_status' => $this->payfastStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    public function submitFormPaystack()
    {
        $this->validate([
            'paystackKey' => Rule::requiredIf($this->paystackStatus == true && $this->selectPaystackEnvironment == 'live'),
            'paystackSecret' => Rule::requiredIf($this->paystackStatus == true && $this->selectPaystackEnvironment == 'live'),
            'testPaystackKey' => Rule::requiredIf($this->paystackStatus == true && $this->selectPaystackEnvironment == 'sandbox'),
            'testPaystackSecret' => Rule::requiredIf($this->paystackStatus == true && $this->selectPaystackEnvironment == 'sandbox'),
            'paystackMerchantEmail' => Rule::requiredIf($this->paystackStatus == true && $this->selectPaystackEnvironment == 'live'),
            'testPaystackMerchantEmail' => Rule::requiredIf($this->paystackStatus == true && $this->selectPaystackEnvironment == 'sandbox'),
        ]);

        $configError = 0;

        // Determine credentials based on selected environment
        $paystackSecret = $this->selectPaystackEnvironment == 'live' ? $this->paystackSecret : $this->testPaystackSecret;

        // Test Paystack credentials
        if ($this->paystackStatus) {
            try {
                $response = Http::withToken($paystackSecret)
                    ->get('https://api.paystack.co/transaction');

                if ($response->successful()) {
                    $this->paymentGateway->update([
                        'paystack_mode' => $this->selectPaystackEnvironment,
                        'live_paystack_key' => $this->paystackKey,
                        'live_paystack_secret' => $this->paystackSecret,
                        'live_paystack_merchant_email' => $this->paystackMerchantEmail,
                        'test_paystack_key' => $this->testPaystackKey,
                        'test_paystack_secret' => $this->testPaystackSecret,
                        'test_paystack_merchant_email' => $this->testPaystackMerchantEmail,
                    ]);
                } else {
                    $configError = 1;
                    $this->addError('paystackKey', 'Invalid Paystack key or secret.');
                    $this->addError('testPaystackKey', 'Invalid Paystack key or secret.');
                }
            } catch (\Exception $e) {
                $this->addError('paystackKey', 'Invalid Paystack key or secret.');
                $this->addError('testPaystackKey', 'Invalid Paystack key or secret.');
            }
        }

        $this->paymentGateway->update([
            'paystack_status' => $this->paystackStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    public function submitFormXendit()
    {
        if ($this->xenditStatus) {
            $this->validate([
                'testXenditPublicKey' => Rule::requiredIf($this->xenditMode == 'sandbox'),
                'testXenditSecretKey' => Rule::requiredIf($this->xenditMode == 'sandbox'),
                'liveXenditPublicKey' => Rule::requiredIf($this->xenditMode == 'live'),
                'liveXenditSecretKey' => Rule::requiredIf($this->xenditMode == 'live'),
            ]);
        }

        $configError = $this->saveXenditSettings();

        $this->paymentGateway->update([
            'xendit_status' => $this->xenditStatus
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    private function saveXenditSettings()
    {
        if (!$this->xenditStatus) {
            return 0;
        }

        try {
            $secretKey = $this->xenditMode === 'live' ? $this->liveXenditSecretKey : $this->testXenditSecretKey;

            // Test Xendit API connectivity
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
                'Content-Type' => 'application/json'
            ])->get('https://api.xendit.co/balance');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'xendit_mode' => $this->xenditMode,
                    'test_xendit_public_key' => $this->testXenditPublicKey,
                    'test_xendit_secret_key' => $this->testXenditSecretKey,
                    'live_xendit_public_key' => $this->liveXenditPublicKey,
                    'live_xendit_secret_key' => $this->liveXenditSecretKey,
                    'test_xendit_webhook_token' => $this->testXenditWebhookToken,
                    'live_xendit_webhook_token' => $this->liveXenditWebhookToken,
                    'xendit_status' => $this->xenditStatus,
                ]);
                return 0;
            }

            $field = $this->xenditMode === 'live' ? 'liveXenditPublicKey' : 'testXenditPublicKey';
            $this->addError($field, 'Invalid Xendit key or secret.');
        } catch (\Exception $e) {
            $field = $this->xenditMode === 'live' ? 'liveXenditPublicKey' : 'testXenditPublicKey';
            $this->addError($field, 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFormPaddle()
    {
        $this->validate([
            'testPaddleVendorId' => Rule::requiredIf($this->paddleStatus == true && $this->paddleMode == 'sandbox'),
            'testPaddleApiKey' => Rule::requiredIf($this->paddleStatus == true && $this->paddleMode == 'sandbox'),
            'testPaddleClientToken' => Rule::requiredIf($this->paddleStatus == true && $this->paddleMode == 'sandbox'),
            'livePaddleVendorId' => Rule::requiredIf($this->paddleStatus == true && $this->paddleMode == 'live'),
            'livePaddleApiKey' => Rule::requiredIf($this->paddleStatus == true && $this->paddleMode == 'live'),
            'livePaddleClientToken' => Rule::requiredIf($this->paddleStatus == true && $this->paddleMode == 'live'),
        ]);

        $configError = 0;

        // Get the appropriate credentials based on environment
        $paddleVendorId = $this->paddleMode == 'live' ? $this->livePaddleVendorId : $this->testPaddleVendorId;
        $paddleApiKey = $this->paddleMode == 'live' ? $this->livePaddleApiKey : $this->testPaddleApiKey;

        // Determine the correct API endpoint based on environment
        $paddleApiUrl = $this->paddleMode == 'live'
            ? 'https://api.paddle.com'
            : 'https://sandbox-api.paddle.com';

        if ($this->paddleStatus) {

            try {
                    // Paddle API uses Bearer token authentication
                    $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $paddleApiKey,
                    'Content-Type' => 'application/json'
                ])->get($paddleApiUrl . '/products');

                if ($response->successful() || $response->status() == 200) {
                    $this->paymentGateway->update([
                        'paddle_mode' => $this->paddleMode,
                        'test_paddle_vendor_id' => $this->testPaddleVendorId,
                        'test_paddle_api_key' => $this->testPaddleApiKey,
                        'test_paddle_client_token' => $this->testPaddleClientToken,
                        'live_paddle_vendor_id' => $this->livePaddleVendorId,
                        'live_paddle_api_key' => $this->livePaddleApiKey,
                        'live_paddle_client_token' => $this->livePaddleClientToken,
                        'paddle_webhook_secret' => $this->paddleWebhookSecret,
                    ]);
                } else {
                    $configError = 1;
                    $field = $this->paddleMode == 'live' ? 'livePaddleApiKey' : 'testPaddleApiKey';
                    $errorMessage = $response->json()['error']['detail'] ?? 'Invalid Paddle API key.';
                    $this->addError($field, $errorMessage);
                }
            } catch (\Exception $e) {
                $configError = 1;
                $field = $this->paddleMode == 'live' ? 'livePaddleApiKey' : 'testPaddleApiKey';
                $this->addError($field, 'Error connecting to Paddle: ' . $e->getMessage());
            }
        }

        $this->paymentGateway->update([
            'paddle_status' => $this->paddleStatus,
            'paddle_webhook_secret' => $this->paddleWebhookSecret
        ]);

        $this->paymentGateway->fresh();
        $this->dispatch('settingsUpdated');
        cache()->forget('superadminPaymentGateway');

        if ($configError == 0) {
            $this->alert('success', __('messages.settingsUpdated'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
        }



    public function render()
    {
        return view('livewire.settings.superadmin-payment-settings');
    }

}
