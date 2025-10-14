<?php

namespace App\Livewire\Settings;

use App\Helper\Files;
use Livewire\Component;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentGatewayCredential;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class PaymentSettings extends Component
{

    use LivewireAlert, WithFileUploads;

    public $razorpaySecret;
    public $razorpayKey;
    public $razorpayStatus;
    public $isRazorpayEnabled;
    public $isStripeEnabled;
    public $offlinePaymentMethod;
    public $paymentGateway;
    public $stripeSecret;
    public $activePaymentSetting = null;
    public $stripeKey;
    public bool $stripeStatus;
    public bool $enableForDineIn;
    public bool $enableForDelivery;
    public bool $enableForPickup;
    public $enableCashPayment = false;
    public $enableQrPayment = false;
    public $paymentDetails;
    public $qrCodeImage;
    public $isofflinepaymentEnabled;
    public bool $enablePayViaCash;
    public bool $enableOfflinePayment;
    public $webhookUrl;
    public $flutterwaveMode;
    public $flutterwaveStatus;
    public $liveFlutterwaveKey;
    public $liveFlutterwaveSecret;
    public $liveFlutterwaveHash;
    public $testFlutterwaveKey;
    public $testFlutterwaveSecret;
    public $testFlutterwaveHash;
    public $testFlutterwaveWebhookKey;
    public $isFlutterwaveEnabled;
    public $isPaypalEnabled;
    public $paypalStatus;
    public $paypalMode;
    public $sandboxPaypalClientId;
    public $sandboxPaypalSecret;
    public $livePaypalClientId;
    public $livePaypalSecret;
    public $webhookRoute;
    public $payfastMerchantId;
    public $payfastMerchantKey;
    public $payfastPassphrase;
    public $payfastMode;
    public $payfastStatus;
    public $testPayfastMerchantId;
    public $testPayfastMerchantKey;
    public $testPayfastPassphrase;
    public $isPayfastEnabled;
    public bool $isGlobalRazorpayEnabled = false;
    public bool $isGlobalStripeEnabled = false;
    public bool $isGlobalFlutterwaveEnabled = false;
    public bool $isGlobalPaypalEnabled = false;
    public bool $isGlobalPayfastEnabled = false;
    public bool $isGlobalPaystackEnabled = false;
    public bool $isGlobalPaddleEnabled = false;

    public $paystackKey;
    public $paystackSecret;
    public $paystackMerchantEmail;
    public $paystackStatus;
    public $isPaystackEnabled;
    public $paystackMode;
    public $testPaystackKey;
    public $testPaystackSecret;
    public $testPaystackMerchantEmail;

    // Xendit properties
    public $xenditPublicKey;
    public $xenditSecretKey;
    public $xenditStatus;
    public $xenditMode;
    public $testXenditPublicKey;
    public $testXenditSecretKey;
    public $liveXenditPublicKey;
    public $liveXenditSecretKey;
    public $testXenditWebhookToken;
    public $liveXenditWebhookToken;
    public $isXenditEnabled;
    public bool $isGlobalXenditEnabled = false;


    public function mount()
    {
         $settings = GlobalSetting::first();

        $this->isGlobalRazorpayEnabled = (bool) $settings->enable_razorpay;
        $this->isGlobalStripeEnabled = (bool) $settings->enable_stripe;
        $this->isGlobalFlutterwaveEnabled = (bool) $settings->enable_flutterwave;
        $this->isGlobalPaypalEnabled = (bool) $settings->enable_paypal;
        $this->isGlobalPayfastEnabled = (bool) $settings->enable_payfast;
        $this->isGlobalPaystackEnabled = (bool) $settings->enable_paystack;
        $this->isGlobalXenditEnabled = (bool) $settings->enable_xendit;
        $this->isGlobalPaddleEnabled = (bool) $settings->enable_paddle;
        $this->paymentGateway = PaymentGatewayCredential::first();

        $this->setDefaultActivePaymentSetting();

        $this->setCredentials();
    }


    private function setDefaultActivePaymentSetting()
    {
        if ($this->activePaymentSetting !== null) {
            return;
        }

        $paymentGateways = [
            'razorpay' => $this->isGlobalRazorpayEnabled,
            'stripe' => $this->isGlobalStripeEnabled,
            'flutterwave' => $this->isGlobalFlutterwaveEnabled,
            'paypal' => $this->isGlobalPaypalEnabled,
            'payfast' => $this->isGlobalPayfastEnabled,
            'paystack' => $this->isGlobalPaystackEnabled,
            'xendit' => $this->isGlobalXenditEnabled,
            'paddle' => $this->isGlobalPaddleEnabled,
            'offline' => true,
            'qr_code' => true,
            'serviceSpecific' => true,
        ];

        // Find the first enabled payment gateway
        foreach ($paymentGateways as $gateway => $isEnabled) {
            if ($isEnabled) {
                $this->activePaymentSetting = $gateway;
                break;
            }
        }

        if ($this->activePaymentSetting === null) {
            $this->activePaymentSetting = 'serviceSpecific';
        }
    }

    public function activeSetting($tab)
    {
        $paymentGateways = [
            'razorpay' => $this->isGlobalRazorpayEnabled,
            'stripe' => $this->isGlobalStripeEnabled,
            'flutterwave' => $this->isGlobalFlutterwaveEnabled,
            'paypal' => $this->isGlobalPaypalEnabled,
            'payfast' => $this->isGlobalPayfastEnabled,
            'paystack' => $this->isGlobalPaystackEnabled,
            'xendit' => $this->isGlobalXenditEnabled,
            'paddle' => $this->isGlobalPaddleEnabled,
            'offline' => true,
            'qr_code' => true,
            'serviceSpecific' => true,
        ];

        if (isset($paymentGateways[$tab]) && $paymentGateways[$tab]) {
            $this->activePaymentSetting = $tab;
        } else {
            $this->activePaymentSetting = null;
            $this->setDefaultActivePaymentSetting();
        }

        $this->setCredentials();
    }

    private function setCredentials()
    {
        $this->razorpayKey = $this->paymentGateway->razorpay_key;
        $this->razorpaySecret = $this->paymentGateway->razorpay_secret;
        $this->razorpayStatus = (bool)$this->paymentGateway->razorpay_status;

        $this->stripeKey = $this->paymentGateway->stripe_key;
        $this->stripeSecret = $this->paymentGateway->stripe_secret;
        $this->stripeStatus = (bool)$this->paymentGateway->stripe_status;

        $this->isRazorpayEnabled = $this->paymentGateway->razorpay_status;
        $this->isStripeEnabled = $this->paymentGateway->stripe_status;
        $this->isFlutterwaveEnabled = $this->paymentGateway->flutterwave_status;

        $this->enableForDineIn = $this->paymentGateway->is_dine_in_payment_enabled;
        $this->enableForDelivery = $this->paymentGateway->is_delivery_payment_enabled;
        $this->enableForPickup = $this->paymentGateway->is_pickup_payment_enabled;

        $this->enableOfflinePayment = (bool)$this->paymentGateway->is_offline_payment_enabled;
        $this->enableQrPayment = (bool)$this->paymentGateway->is_qr_payment_enabled;
        $this->paymentDetails = $this->paymentGateway->offline_payment_detail;
        $this->qrCodeImage = $this->paymentGateway->qr_code_image_url;
        $this->enablePayViaCash = (bool)$this->paymentGateway->is_cash_payment_enabled;

        $this->flutterwaveMode = $this->paymentGateway->flutterwave_mode;
        $this->flutterwaveStatus = (bool)$this->paymentGateway->flutterwave_status;
        $this->liveFlutterwaveKey = $this->paymentGateway->live_flutterwave_key;
        $this->liveFlutterwaveSecret = $this->paymentGateway->live_flutterwave_secret;
        $this->liveFlutterwaveHash = $this->paymentGateway->live_flutterwave_hash;

        $this->testFlutterwaveKey = $this->paymentGateway->test_flutterwave_key;
        $this->testFlutterwaveSecret = $this->paymentGateway->test_flutterwave_secret;
        $this->testFlutterwaveHash = $this->paymentGateway->test_flutterwave_hash;

        $this->isPaypalEnabled = $this->paymentGateway->paypal_status;
        $this->paypalStatus = (bool) $this->paymentGateway->paypal_status;
        $this->paypalMode = $this->paymentGateway->paypal_mode;
        $this->sandboxPaypalClientId = $this->paymentGateway->sandbox_paypal_client_id;
        $this->sandboxPaypalSecret = $this->paymentGateway->sandbox_paypal_secret;
        $this->livePaypalClientId = $this->paymentGateway->paypal_client_id;
        $this->livePaypalSecret = $this->paymentGateway->paypal_secret;

        $this->payfastMerchantId = $this->paymentGateway->payfast_merchant_id;
        $this->payfastMerchantKey = $this->paymentGateway->payfast_merchant_key;
        $this->payfastPassphrase = $this->paymentGateway->payfast_passphrase;
        $this->payfastMode = $this->paymentGateway->payfast_mode;
        $this->payfastStatus = (bool)$this->paymentGateway->payfast_status;
        $this->testPayfastMerchantId = $this->paymentGateway->test_payfast_merchant_id;
        $this->testPayfastMerchantKey = $this->paymentGateway->test_payfast_merchant_key;
        $this->testPayfastPassphrase = $this->paymentGateway->test_payfast_passphrase;
        $this->isPayfastEnabled = $this->paymentGateway->payfast_status;

         $this->paystackStatus = (bool)$this->paymentGateway->paystack_status;
        $this->paystackKey = $this->paymentGateway->paystack_key;
        $this->paystackSecret = $this->paymentGateway->paystack_secret;
        $this->paystackMerchantEmail = $this->paymentGateway->paystack_merchant_email;
        $this->paystackMode = $this->paymentGateway->paystack_mode;
        $this->testPaystackKey = $this->paymentGateway->test_paystack_key;
        $this->testPaystackSecret = $this->paymentGateway->test_paystack_secret;
        $this->testPaystackMerchantEmail = $this->paymentGateway->test_paystack_merchant_email;
        $this->isPaystackEnabled = $this->paymentGateway->paystack_status;

        // Xendit credentials
        $this->xenditStatus = (bool)$this->paymentGateway->xendit_status;
        $this->xenditMode = $this->paymentGateway->xendit_mode;
        $this->testXenditPublicKey = $this->paymentGateway->test_xendit_public_key;
        $this->testXenditSecretKey = $this->paymentGateway->test_xendit_secret_key;
        $this->liveXenditPublicKey = $this->paymentGateway->live_xendit_public_key;
        $this->liveXenditSecretKey = $this->paymentGateway->live_xendit_secret_key;
        $this->testXenditWebhookToken = $this->paymentGateway->test_xendit_webhook_token;
        $this->liveXenditWebhookToken = $this->paymentGateway->live_xendit_webhook_token;
        $this->isXenditEnabled = $this->paymentGateway->xendit_status;

        $hash = restaurant()->hash;
        $this->testFlutterwaveWebhookKey = $this->paymentGateway->flutterwave_webhook_secret_hash ? $this->paymentGateway->flutterwave_webhook_secret_hash : substr(md5($hash), 0, 10);

        if ($this->activePaymentSetting === 'flutterwave') {
            $this->webhookUrl = route('flutterwave.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'paypal') {
            $this->webhookUrl = route('paypal.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'paystack') {
            $this->webhookUrl = route('paystack.webhook', ['hash' => $hash]);
        }
        if ($this->activePaymentSetting === 'xendit') {
            $this->webhookUrl = route('xendit.webhook', ['hash' => $hash]);
        }
    }

    public function submitFormServiceSpecific()
    {
        $this->paymentGateway->update([
            'is_dine_in_payment_enabled' => $this->enableForDineIn,
            'is_delivery_payment_enabled' => $this->enableForDelivery,
            'is_pickup_payment_enabled' => $this->enableForPickup,
        ]);
        $this->updatePaymentStatus();
        $this->alertSuccess();
    }

    public function submitFormRazorpay()
    {
        $this->validate([
            'razorpaySecret' => 'required_if:razorpayStatus,true',
            'razorpayKey' => 'required_if:razorpayStatus,true',
        ]);

        if ($this->saveRazorpaySettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }
    }

    public function submitFormStripe()
    {
        $this->validate([
            'stripeSecret' => 'required_if:stripeStatus,true',
            'stripeKey' => 'required_if:stripeStatus,true',
        ]);

        if ($this->saveStripeSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }
    }

    public function submitFormOffline()
    {
        $rules = [
            'enableOfflinePayment' => 'required|boolean',
            'enableQrPayment' => 'required|boolean',
            'enablePayViaCash' => 'required|boolean'
        ];

        if ($this->enableOfflinePayment) {
            $rules['paymentDetails'] = 'required|string|max:1000';
        }

        if ($this->enableQrPayment && !$this->paymentGateway->qr_code_image) {
            $rules['qrCodeImage'] = 'required|image|max:1024';
        }

        $this->validate($rules);

        // Upload QR code image if enabled and valid
        if ($this->enableQrPayment && is_object($this->qrCodeImage) && $this->qrCodeImage->isValid()) {
            $this->qrCodeImage = Files::uploadLocalOrS3($this->qrCodeImage, PaymentGatewayCredential::QR_CODE_FOLDER, width: 800);
        } else {
            $this->qrCodeImage = $this->paymentGateway->qr_code_image;
        }


        $updateData = [
            'is_offline_payment_enabled' => $this->enableOfflinePayment,
            'offline_payment_detail' => $this->enableOfflinePayment ? $this->paymentDetails : $this->paymentDetails,
            'is_qr_payment_enabled' => $this->enableQrPayment,
            'qr_code_image' => $this->qrCodeImage,
            'is_cash_payment_enabled' => $this->enablePayViaCash,

        ];

        $this->paymentGateway->update($updateData);

        $this->updatePaymentStatus();
        $this->alertSuccess();
    }

    private function saveRazorpaySettings()
    {
        if (!$this->razorpayStatus) {
            $this->paymentGateway->update([
                'razorpay_status' => $this->razorpayStatus,
            ]);
            return 0;
        }

        try {
            $response = Http::withBasicAuth($this->razorpayKey, $this->razorpaySecret)
                ->get('https://api.razorpay.com/v1/contacts');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'razorpay_key' => $this->razorpayKey,
                    'razorpay_secret' => $this->razorpaySecret,
                    'razorpay_status' => $this->razorpayStatus,
                ]);
                return 0;
            }

            $this->addError('razorpayKey', 'Invalid Razorpay key or secret.');
        } catch (\Exception $e) {
            $this->addError('razorpayKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    private function saveStripeSettings()
    {

        if (!$this->stripeStatus) {
            $this->paymentGateway->update([
                'stripe_status' => $this->stripeStatus,
            ]);
            return 0;
        }

        try {
            $response = Http::withToken($this->stripeSecret)
                ->get('https://api.stripe.com/v1/customers');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'stripe_key' => $this->stripeKey,
                    'stripe_secret' => $this->stripeSecret,
                    'stripe_status' => $this->stripeStatus,
                ]);
                return 0;
            }

            $this->addError('stripeKey', 'Invalid Stripe key or secret.');
        } catch (\Exception $e) {
            $this->addError('stripeKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFlutterwaveForm()
    {

        $this->validate(
            [
                'flutterwaveStatus' => 'required|boolean',
                'flutterwaveMode' => 'required_if:flutterwaveStatus,true',
                'liveFlutterwaveKey' => 'required_if:flutterwaveMode,live',
                'liveFlutterwaveSecret' => 'required_if:flutterwaveMode,live',
                'liveFlutterwaveHash' => 'required_if:flutterwaveMode,live',
                'testFlutterwaveKey' => 'required_if:flutterwaveMode,test',
                'testFlutterwaveSecret' => 'required_if:flutterwaveMode,test',
                'testFlutterwaveHash' => 'required_if:flutterwaveMode,test',
            ],
            [
                'flutterwaveStatus.required' => __('validation.flutterwaveStatusRequired'),
                'flutterwaveMode.required_if' => __('validation.flutterwaveModeRequired'),
                'liveFlutterwaveKey.required_if' => __('validation.liveFlutterwaveKeyRequired'),
                'liveFlutterwaveSecret.required_if' => __('validation.liveFlutterwaveSecretRequired'),
                'liveFlutterwaveHash.required_if' => __('validation.liveFlutterwaveHashRequired'),
                'testFlutterwaveKey.required_if' => __('validation.testFlutterwaveKeyRequired'),
                'testFlutterwaveSecret.required_if' => __('validation.testFlutterwaveSecretRequired'),
                'testFlutterwaveHash.required_if' => __('validation.testFlutterwaveHashRequired'),
            ]
        );
        if ($this->saveFlutterwaveSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }
    }

    private function saveFlutterwaveSettings()
    {

        if (!$this->flutterwaveStatus) {
            $this->paymentGateway->update([
                'flutterwave_status' => $this->flutterwaveStatus,
            ]);

            return 0;
        }

        try {
            $apiSecret = $this->flutterwaveMode === 'live' ? $this->liveFlutterwaveSecret : $this->testFlutterwaveSecret;

            $response = Http::withToken($apiSecret)
                ->get('https://api.flutterwave.com/v3/transactions');

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'flutterwave_mode' => $this->flutterwaveMode,
                    'flutterwave_status' => $this->flutterwaveStatus,
                    'live_flutterwave_key' => $this->liveFlutterwaveKey,
                    'live_flutterwave_secret' => $this->liveFlutterwaveSecret,
                    'live_flutterwave_hash' => $this->liveFlutterwaveHash,
                    'test_flutterwave_key' => $this->testFlutterwaveKey,
                    'test_flutterwave_secret' => $this->testFlutterwaveSecret,
                    'test_flutterwave_hash' => $this->testFlutterwaveHash,
                    'flutterwave_webhook_secret_hash' => $this->testFlutterwaveWebhookKey,
                ]);
                return 0;
            }

            $this->addError(
                $this->flutterwaveMode === 'live' ? 'liveFlutterwaveKey' : 'testFlutterwaveKey',
                __('validation.InvalidFlutterwaveKeyOrSecret')
            );
        } catch (\Exception $e) {
            $this->addError('flutterwaveKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }
    public function submitFormPaypal()
    {
        $this->validate([
            'paypalStatus' => 'required|boolean',
            'paypalMode' => 'required_if:paypalStatus,true', // live or sandbox
            'livePaypalClientId' => 'required_if:paypalMode,live',
            'livePaypalSecret' => 'required_if:paypalMode,live',
            'sandboxPaypalClientId' => 'required_if:paypalMode,sandbox',
            'sandboxPaypalSecret' => 'required_if:paypalMode,sandbox',
        ]);

        if ($this->savePaypalSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus) {
            $this->enablePayViaCash = true;
        }
    }

    public function savePaypalSettings()
    {


        if (!$this->paypalStatus) {
            $this->paymentGateway->update([
                'paypal_status' => $this->paypalStatus,
            ]);
            return 0;
        }

        try {
            $apiKey = $this->paypalMode === 'live' ? $this->livePaypalClientId : $this->sandboxPaypalClientId;
            $apiSecret = $this->paypalMode === 'live' ? $this->livePaypalSecret : $this->sandboxPaypalSecret;

            $url = $this->paypalMode === 'live'
                ? 'https://api.paypal.com/v1/oauth2/token'
                : 'https://api.sandbox.paypal.com/v1/oauth2/token';

            $response = Http::withBasicAuth($apiKey, $apiSecret)
                ->asForm()
                ->post($url, [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                $this->paymentGateway->update([
                    'paypal_mode' => $this->paypalMode,
                    'paypal_status' => $this->paypalStatus,
                    'sandbox_paypal_client_id' => $this->sandboxPaypalClientId,
                    'sandbox_paypal_secret' => $this->sandboxPaypalSecret,
                    'paypal_client_id' => $this->livePaypalClientId,
                    'paypal_secret' => $this->livePaypalSecret,
                ]);
                return 0;
            }

            $this->addError('paypalKey', 'Invalid Paypal key or secret.');
        } catch (\Exception $e) {
            $this->addError('paypalKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function submitFormPayfast()
    {
        $this->validate([
            'testPayfastMerchantId' => 'nullable|required_if:payfastMode,sandbox',
            'testPayfastMerchantKey' => 'nullable|required_if:payfastMode,sandbox',
            'payfastMerchantId' => 'nullable|required_if:payfastMode,live',
            'payfastMerchantKey' => 'nullable|required_if:payfastMode,live',
            'payfastPassphrase' => 'nullable|required_if:payfastMode,live',
        ]);

        if ($this->savePayfastSettings() === 0) {

            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function savePayfastSettings()
    {

        if (!$this->payfastStatus) {
            $this->paymentGateway->update([
                'payfast_status' => $this->payfastStatus,
            ]);
            return 0;
        }

        try {
            $this->paymentGateway->update([
                'payfast_merchant_id' => $this->payfastMerchantId,
                'payfast_merchant_key' => $this->payfastMerchantKey,
                'payfast_passphrase' => $this->payfastPassphrase,
                'payfast_mode' => $this->payfastMode,
                'test_payfast_merchant_id' => $this->testPayfastMerchantId,
                'test_payfast_merchant_key' => $this->testPayfastMerchantKey,
                'test_payfast_passphrase' => $this->testPayfastPassphrase,
                'payfast_status' => $this->payfastStatus,
            ]);

            return 0;
        } catch (\Exception $e) {
            $this->addError('payfastKey', 'Error saving Payfast settings: ' . $e->getMessage());
            return 1;
        }
    }


    public function submitFormPaystack()
    {

        $this->validate([
            'testPaystackKey' => 'nullable|required_if:paystackMode,sandbox',
            'testPaystackSecret' => 'nullable|required_if:paystackMode,sandbox',
            'testPaystackMerchantEmail' => 'nullable|required_if:paystackMode,sandbox|email',

            'paystackKey' => 'nullable|required_if:paystackMode,live',
            'paystackSecret' => 'nullable|required_if:paystackMode,live',
            'paystackMerchantEmail' => 'nullable|required_if:paystackMode,live|email',
            ]);

        if ($this->savePaystackSettings() === 0) {

            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function savePaystackSettings()
    {
        if (!$this->paystackStatus) {
            $this->paymentGateway->update([
                'paystack_status' => $this->paystackStatus,
            ]);
            return 0;
        }

        try {
            $apiSecret = $this->paystackMode === 'live' ? $this->paystackSecret : $this->testPaystackSecret;

            $response = Http::withToken($apiSecret)
                    ->get('https://api.paystack.co/transaction');
            if ($response->successful()) {
                $this->paymentGateway->update([
                    'paystack_key' => $this->paystackKey,
                    'paystack_secret' => $this->paystackSecret,
                    'paystack_merchant_email' => $this->paystackMerchantEmail,
                    'paystack_mode' => $this->paystackMode,
                    'test_paystack_key' => $this->testPaystackKey,
                    'test_paystack_secret' => $this->testPaystackSecret,
                    'test_paystack_merchant_email' => $this->testPaystackMerchantEmail,
                    'paystack_payment_url' => $this->paymentGateway->paystack_payment_url,
                    'paystack_status' => $this->paystackStatus,
                ]);
                return 0;
            }

            $this->addError('paystackKey', 'Invalid Paystack key or secret.');
        } catch (\Exception $e) {
            $this->addError('paystackKey', 'Error: ' . $e->getMessage());
        }

        return 1;
    }


    public function submitFormXendit()
    {
       if ($this->xenditStatus) {
            $this->validate([
                'testXenditPublicKey' => 'nullable|required_if:xenditMode,sandbox',
                'testXenditSecretKey' => 'nullable|required_if:xenditMode,sandbox',
                'liveXenditPublicKey' => 'nullable|required_if:xenditMode,live',
                'liveXenditSecretKey' => 'nullable|required_if:xenditMode,live',
            ]);
        }

        if ($this->saveXenditSettings() === 0) {
            $this->updatePaymentStatus();
            $this->alertSuccess();
        }

        if (!$this->razorpayStatus && !$this->stripeStatus && !$this->flutterwaveStatus && !$this->paypalStatus && !$this->paystackStatus && !$this->payfastStatus && !$this->xenditStatus) {
            $this->enablePayViaCash = true;
        }
    }

    private function saveXenditSettings()
    {
        if (!$this->xenditStatus) {
            $this->paymentGateway->update([
                'xendit_status' => $this->xenditStatus,
            ]);
            return 0;
        }

        try {
            $publicKey = $this->xenditMode === 'live' ? $this->liveXenditPublicKey : $this->testXenditPublicKey;
            $secretKey = $this->xenditMode === 'live' ? $this->liveXenditSecretKey : $this->testXenditSecretKey;

            // Test Xendit API connection
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


    public function updatePaymentStatus()
    {
        $this->setCredentials();
        $this->dispatch('settingsUpdated');
        session()->forget('paymentGateway');
    }

    public function alertSuccess()
    {
        $this->alert('success', __('messages.settingsUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close'),
        ]);
    }

    public function render()
    {
        return view('livewire.settings.payment-settings');
    }
}
