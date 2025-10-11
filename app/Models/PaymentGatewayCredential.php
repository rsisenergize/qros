<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class PaymentGatewayCredential extends BaseModel
{

    use HasFactory, HasRestaurant;

    protected $guarded = ['id'];

    const QR_CODE_FOLDER = 'qr-codes';


    protected $casts = [
        'stripe_key' => 'encrypted',
        'razorpay_key' => 'encrypted',
        'stripe_secret' => 'encrypted',
        'razorpay_secret' => 'encrypted',
    ];


    protected $appends = [
        'qr_code_image_url',
    ];


    public function qrCodeImageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->qr_code_image ? asset_url_local_s3(self::QR_CODE_FOLDER . '/' . $this->qr_code_image) : '';
        });
    }

    public function getFlutterwaveKeyAttribute()
    {
        return ($this->flutterwave_mode == 'test' ? $this->test_flutterwave_key : $this->live_flutterwave_key);
    }

    public function getFlutterwaveSecretAttribute()
    {
        return ($this->flutterwave_mode == 'test' ? $this->test_flutterwave_secret : $this->live_flutterwave_secret);
    }

    public function getFlutterwaveWebhookKeyAttribute()
    {
        return ($this->flutterwave_mode == 'test' ? $this->flutterwave_test_webhook_key : $this->flutterwave_live_webhook_key);
    }

    public function getPaypalClientIdAttribute()
    {
        return ($this->paypal_mode == 'sandbox' ? $this->sandbox_paypal_client_id : $this->paypal_client_id);
    }
    public function getPaypalSecretAttribute()
    {
        return ($this->paypal_mode == 'sandbox' ? $this->sandbox_paypal_secret : $this->paypal_secret);
    }
     public function getPayfastMerchantIdDataAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_merchant_id : $this->payfast_merchant_id);
    }
    public function getPayfastMerchantKeyDataAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_merchant_key : $this->payfast_merchant_key);
    }

    public function getPayfastPassphraseDataAttribute()
    {
        return ($this->payfast_mode == 'sandbox' ? $this->test_payfast_passphrase : $this->payfast_passphrase);
    }

        public function getPaystackKeyDataAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_key : $this->paystack_key);
    }
    public function getPaystackSecretDataAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_secret : $this->paystack_secret);
    }
    public function getPaystackMerchantEmailDataAttribute()
    {
        return ($this->paystack_mode == 'sandbox' ? $this->test_paystack_merchant_email : $this->paystack_merchant_email);
    }

    public function getXenditPublicKeyAttribute()
    {
        return ($this->xendit_mode == 'sandbox' ? $this->test_xendit_public_key : $this->live_xendit_public_key);
    }

    public function getXenditSecretKeyAttribute()
    {
        return ($this->xendit_mode == 'sandbox' ? $this->test_xendit_secret_key : $this->live_xendit_secret_key);
    }

}
