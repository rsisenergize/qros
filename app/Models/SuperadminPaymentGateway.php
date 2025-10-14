<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class SuperadminPaymentGateway extends BaseModel
{
    protected $guarded = ['id'];

    public function getRazorpayKeyAttribute()
    {
        return ($this->razorpay_type == 'test' ? $this->test_razorpay_key : $this->live_razorpay_key);
    }

    public function getRazorpaySecretAttribute()
    {
        return ($this->razorpay_type == 'test' ? $this->test_razorpay_secret : $this->live_razorpay_secret);
    }

    public function getRazorpayWebhookKeyAttribute()
    {
        return ($this->razorpay_type == 'test' ? $this->razorpay_test_webhook_key : $this->razorpay_live_webhook_key);
    }

    public function getStripeKeyAttribute()
    {
        return ($this->stripe_type == 'test' ? $this->test_stripe_key : $this->live_stripe_key);
    }

    public function getStripeSecretAttribute()
    {
        return ($this->stripe_type == 'test' ? $this->test_stripe_secret : $this->live_stripe_secret);
    }

    public function getStripeWebhookKeyAttribute()
    {
        return ($this->stripe_type == 'test' ? $this->stripe_test_webhook_key : $this->stripe_live_webhook_key);
    }

    public function getFlutterwaveKeyAttribute()
    {
        return ($this->flutterwave_type == 'test' ? $this->test_flutterwave_key : $this->live_flutterwave_key);
    }

    public function getFlutterwaveSecretAttribute()
    {
        return ($this->flutterwave_type == 'test' ? $this->test_flutterwave_secret : $this->live_flutterwave_secret);
    }

    public function getFlutterwaveWebhookKeyAttribute()
    {
        return ($this->flutterwave_type == 'test' ? $this->flutterwave_test_webhook_key : $this->flutterwave_live_webhook_key);
    }

    public function getPaypalClientAttribute()
    {
        return ($this->paypal_mode == 'sandbox' ? $this->test_paypal_client_id : $this->live_paypal_client_id);
    }

    public function getPaypalSecretAttribute()
    {
        return ($this->paypal_mode == 'sandbox' ? $this->test_paypal_secret : $this->live_paypal_secret);
    }

    public function getPayfastMerchantIdAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_merchant_id : $this->live_payfast_merchant_id);
    }

    public function getPayfastMerchantKeyAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_merchant_key : $this->live_payfast_merchant_key);
    }

    public function getPayfastPassphraseAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_passphrase : $this->live_payfast_passphrase);
    }

    public function getPaystackKeyAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_key : $this->live_paystack_key);
    }

    public function getPaystackSecretAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_secret : $this->live_paystack_secret);
    }

    public function getPaystackMerchantEmailAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_merchant_email : $this->live_paystack_merchant_email);
    }

    public function getXenditKeyAttribute()
    {
        return ($this->xendit_mode == 'sandbox' ? $this->test_xendit_public_key : $this->live_xendit_public_key);
    }

    public function getXenditSecretAttribute()
    {
        return ($this->xendit_mode == 'sandbox' ? $this->test_xendit_secret_key : $this->live_xendit_secret_key);
    }




}
