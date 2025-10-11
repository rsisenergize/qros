<?php

namespace App\Http\Controllers\SuperAdmin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\GlobalSubscription;
use App\Http\Controllers\Controller;
use App\Models\SuperadminPaymentGateway;
use stdClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\RestaurantUpdatedPlan;
use App\Models\Restaurant;
use App\Models\Package;
use App\Models\RestaurantPayment;
use App\Models\EmailSetting;

class PayfastController extends Controller
{
    public function initiatePayfastPayment(Request $request)
    {
        $paymentId = $request->payment_id;
        $amount = $request->amount;
        $currency = $request->currency;
        $restaurantId = $request->restaurant_id;
        $packageId = $request->package_id;
        $restaurant = Restaurant::findOrFail($restaurantId);
        $package = Package::findOrFail($packageId);
        $hash = global_setting()->hash;

        $restaurantPayment = RestaurantPayment::findOrFail($request->payment_id);
        $globalInvoice = GlobalInvoice::with('package', 'restaurant', 'currency', 'globalSubscription')
            ->whereNotNull('pay_date')
            ->where('restaurant_id', restaurant()->id)->get();
        $firstInvoice = $globalInvoice->sortByDesc(function ($temp, $key) {
            return Carbon::parse($temp->paid_on)->getTimestamp();
        })->first();


        $credential = new stdClass();
        $globalCredential = SuperadminPaymentGateway::first();

        if ($globalCredential->payfast_mode == 'sandbox') {
            $passphrase = $credential->payfast_salt_passphrase = $globalCredential->test_payfast_passphrase;
            $credential->payfast_key = $globalCredential->test_payfast_merchant_id;
            $credential->payfast_secret = $globalCredential->test_payfast_merchant_key;
            $environment = 'https://sandbox.payfast.co.za/eng/process';
        } else {
            $passphrase = $credential->payfast_salt_passphrase = $globalCredential->live_payfast_passphrase;
            $credential->payfast_key = $globalCredential->live_payfast_merchant_id;
            $credential->payfast_secret = $globalCredential->live_payfast_merchant_key;
            $environment = 'https://www.payfast.co.za/eng/process';
        }


        $randomString = Str::random(30);
        $type = $request->input('package_type');

        if ($package->package_type->value === 'standard') {

            if (!in_array($type, ['monthly', 'annual'])) {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.invalidPlanType'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }
            $amount = $type === 'monthly' ? $package->monthly_price : $package->annual_price;
            $plan = $type === 'monthly' ? '3' : '6';
        } else {
            $type = 'lifetime';
            $amount = $package->price;
            $plan = null;
        }
        $packageId = $package->id;
        $planType = strtolower($package->package_name) . '_' . $type;
        $restaurantId = $restaurant->id;
        $cartTotal = $amount;

        $subscription = GlobalSubscription::where('restaurant_id', $restaurantId)
            ->where('gateway_name', 'payfast')
            ->where('subscription_status', 'inactive')
            ->whereNull('ends_at')
            ->latest()
            ->first();

        $subscription = $subscription ? $subscription : new GlobalSubscription();
        $subscription->restaurant_id = $restaurantId;
        $subscription->package_id = $package->id;
        $subscription->currency_id = $package->currency_id;
        $subscription->package_type = $type;
        $subscription->payfast_plan = $planType;
        $subscription->quantity = 1;
        $subscription->payfast_status = 'active';
        $subscription->gateway_name = 'payfast';
        $subscription->subscription_status = 'inactive';
        $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
        $subscription->save();

        $subscriptionId = $subscription->id;

        $data = [
            'merchant_id' => $credential->payfast_key,
            'merchant_key' => $credential->payfast_secret,
            'return_url' => route('billing.payfast-success', compact('subscriptionId', 'cartTotal', 'restaurantPayment')),
            'cancel_url' => route('billing.payfast-cancel', compact('restaurantPayment')),
            'notify_url' => route('payfast-notification', [$hash], compact('passphrase', 'packageId', 'planType', 'amount', 'type', 'restaurantId')),
            'name_first' => user()->name,
            'email_address' => user()->email,
            'm_payment_id' => $randomString,
            'amount' => number_format($cartTotal, 2, '.', ''),
            'item_name' => $package->package_name . ' ' . ucfirst($type),
            'custom_int1' => restaurant()->id,
            'custom_int2' => $package->id,
            'custom_int3' => $subscriptionId,
            'custom_str1' => $type,
            'custom_str2' => $planType,
        ];

        if ($type !== 'lifetime') {
            $data['subscription_type'] = '1';
            $data['billing_date'] = now()->format('Y-m-d');
            $data['recurring_amount'] = number_format($cartTotal, 2, '.', '');
            $data['frequency'] = $plan;
            $data['cycles'] = '0';
        }

        $signature = $this->generateSignature($data, $credential->payfast_salt_passphrase);

        $data['signature'] = $signature;

        $htmlForm = '<form id="payfast_payment_form" action="' . $environment . '" method="post">';

        foreach ($data as $name => $value) {
            $htmlForm .= '<input name="' . $name . '" type="hidden" value=\'' . $value . '\' />';
        }

        // Auto-submit script
        $htmlForm .= '</form>
            <script>
                document.getElementById("payfast_payment_form").submit();
            </script>';

        return $htmlForm;
    }

    public function generateSignature($data, $passPhrase = null)
    {
        // Create parameter string
        $pfOutput = '';

        foreach ($data as $key => $val) {

            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        // Remove last ampersand
        $getString = substr($pfOutput, 0, -1);

        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }

        return md5($getString);
    }

    public function payFastPaymentSuccess(Request $request)
    {
        try {
            $subscription = GlobalSubscription::find($request->subscriptionId);

            if ($subscription) {
                $subscription->subscription_status = 'active';
                $subscription->save();

                $invoice = GlobalInvoice::where('global_subscription_id', $subscription->id)->first();
                $invoice = $invoice ? $invoice : new GlobalInvoice();
                $invoice->restaurant_id = $subscription->restaurant_id;
                $invoice->package_id = $subscription->package_id;
                $invoice->currency_id = $subscription->currency_id;
                $invoice->global_subscription_id = $subscription->id;
                $invoice->pay_date = now()->format('Y-m-d');
                $invoice->next_pay_date = now()->{(($subscription->package_type == 'monthly') ? 'addMonth' : 'addYear')}()->format('Y-m-d');
                $invoice->status = 'active';
                $invoice->package_type = $subscription->package_type;
                $invoice->gateway_name = 'payfast';
                $invoice->total = $request->cartTotal;
                $invoice->save();

                $restaurant = restaurant();
                $restaurant->package_id = $subscription->package_id;
                $restaurant->package_type = $subscription->package_type;

                // Set restaurant status active
                $restaurant->status = 'active';
                $restaurant->license_expire_on = null;
                $restaurant->save();

                $restaurantPayment = RestaurantPayment::findOrFail($request->restaurantPayment);
                $restaurantPayment->update([
                    'status' => 'paid',
                    'payment_date_time' => now(),
                    'amount' => $request->cartTotal
                ]);

                $emailSetting = EmailSetting::first();

                if ($emailSetting->mail_driver === 'smtp' && $emailSetting->verified) {
                    $generatedBy = User::withoutGlobalScopes()->whereNull('restaurant_id')->first();
                    Notification::send($generatedBy, new RestaurantUpdatedPlan($restaurant, $subscription->package_id));

                    // Notify restaurant admin
                    $restaurantAdmin = $restaurant->restaurantAdmin($restaurant);
                    Notification::send($restaurantAdmin, new RestaurantUpdatedPlan($restaurant, $subscription->package_id));
                }

                session()->forget('restaurant');
                request()->session()->flash('flash.banner', __('messages.planUpgraded'));
                request()->session()->flash('flash.bannerStyle', 'success');
                request()->session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));

                return redirect()->route('dashboard')->with('livewire', true);
            }

            return Redirect::route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.paymentError'),
                'flash.bannerStyle' => 'danger'
            ]);
        }
    }

    public function payFastPaymentCancel(Request $request)
    {
        $restaurantPayment = RestaurantPayment::findOrFail($request->restaurantPayment);

        $restaurantPayment->update([
            'status' => 'failed',
            'payment_date_time' => now(),
        ]);
        return redirect()->route('dashboard')->with([
            'flash.banner' => __('messages.paymentError'),
            'flash.bannerStyle' => 'danger'
        ]);
    }
}
