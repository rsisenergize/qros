<?php

namespace App\Http\Controllers\SuperAdmin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Package;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\GlobalInvoice;
use App\Models\RestaurantPayment;
use App\Models\GlobalSubscription;
use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Support\Facades\Http;
use App\Models\SuperadminPaymentGateway;
use App\Notifications\RestaurantUpdatedPlan;
use Illuminate\Support\Facades\Notification;

class FlutterwaveController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $paymentGateway = SuperadminPaymentGateway::first();
        $restaurantPayment = RestaurantPayment::findOrFail($request->payment_id);
        $restaurant = restaurant()::find($restaurantPayment->restaurant_id);
        $package = Package::find($request->input('package_id'));

        if (!$package) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.packageNotFound'),
                'flash.bannerStyle' => 'danger'
            ]);
        }
        $currencyCode = $restaurant->currency->currency_code;
        if ($package->package_type->value === 'standard') {
            $planType = $request->input('package_type');
            $planId = $planType === 'annual' ? $package->flutterwave_annual_plan_id : $package->flutterwave_monthly_plan_id;

            // Check if the plan ID exists
            if (!isset($planId) || trim($planId) === '') {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.invalidFlutterwavePlan'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            // Verify plan ID exists in Flutterwave
            $planCheckResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
                'Content-Type' => 'application/json',
            ])->get("https://api.flutterwave.com/v3/payment-plans/{$planId}");

            if ($planCheckResponse->failed() || !isset($planCheckResponse->json()['data'])) {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.flutterwavePlanNotFound'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            $transactionRef = "FLW_" . uniqid();
            $redirectUrl = route('flutterwave.callback');
            $planDetails = $planCheckResponse->json()['data'];
            $currencyCode = $planDetails['currency'];
            $amount = $planDetails['amount'];

            $payload = [
                'tx_ref' => $transactionRef,
                'amount' => $amount,
                'currency' => $currencyCode,
                'payment_options' => 'card, banktransfer',
                'redirect_url' => $redirectUrl,
                'customer' => [
                    'email' => $restaurant->email,
                    'name' => $restaurant->name,
                ],
                'customizations' => [
                    'title' => 'License Payment',
                    'description' => 'Payment for Restaurant License',
                ],
                'payment_plan' => $planId // Attach the plan ID
            ];
        } else {
            // Lifetime package or other one-time payment
            $transactionRef = "FLW_" . uniqid();
            $redirectUrl = route('flutterwave.callback');

            $payload = [
                'tx_ref' => $transactionRef,
                'amount' => $restaurantPayment->amount,
                'currency' => $currencyCode,
                'payment_options' => 'card, banktransfer',
                'redirect_url' => $redirectUrl,
                'customer' => [
                    'email' => $restaurant->email,
                    'name' => $restaurant->name,
                ],
                'customizations' => [
                    'title' => 'License Payment',
                    'description' => 'Payment for Restaurant License',
                ]
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
            'Content-Type' => 'application/json',
        ])->post('https://api.flutterwave.com/v3/payments', $payload);

        $responseData = $response->json();
        if (isset($responseData['data']['link'])) {
            $restaurantPayment->flutterwave_payment_ref = $transactionRef;
            $restaurantPayment->save();
            return redirect()->away($responseData['data']['link']);
        }

        return redirect()->route('dashboard')->with([
            'flash.banner' => __('messages.paymentError'),
            'flash.bannerStyle' => 'danger'
        ]);
    }

    public function paymentCallback(Request $request)
    {
        $paymentGateway = SuperadminPaymentGateway::first();
        $transactionRef = $request->query('tx_ref');

        if (!$transactionRef) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.transactionReferenceMissing'),
                'flash.bannerStyle' => 'danger'
            ]);
        }

        $restaurantPayment = RestaurantPayment::where('flutterwave_payment_ref', $transactionRef)->first();
        if (!$restaurantPayment) {
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.invalidTransactionReference'),
                'flash.bannerStyle' => 'danger'
            ]);
        }

        try {


            // Verify the transaction with Flutterwave API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
                'Content-Type' => 'application/json',
            ])->get("https://api.flutterwave.com/v3/transactions/{$request->query('transaction_id')}/verify");

            $responseData = $response->json();

            if (!isset($responseData['status']) || !isset($responseData['data'])) {
                $restaurantPayment->status = 'failed';
                $restaurantPayment->save();
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.paymentVerificationFailed'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            $paymentSuccess = ($responseData['status'] === 'success' && $responseData['data']['status'] === 'successful');

            if (!$paymentSuccess) {
                $restaurantPayment->status = 'failed';
                $restaurantPayment->save();
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.paymentVerificationFailed'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }

            $FlutterwaveTransactionId = $responseData['data']['id'] ?? null;
            $subscriptionId = null;
            if ($FlutterwaveTransactionId) {

                $sub = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $paymentGateway->flutterwave_secret,
                    'Content-Type' => 'application/json',
                ])->get("https://api.flutterwave.com/v3/subscriptions", [
                    'transaction_id' => $FlutterwaveTransactionId,
                    'status' => 'active',
                ]);

                if (isset($sub['data']) && is_array($sub['data']) && count($sub['data']) > 0) {
                    $subscriptionId = $sub['data'][0]['id'] ?? null;
                }
            }

            $transactionId = $responseData['data']['tx_ref'] ?? null;
            $amount = $responseData['data']['amount'] ?? null;
            $customerId = $responseData['data']['customer']['id'] ?? null;

            $restaurantPayment->flutterwave_transaction_id = $transactionId;
            $restaurantPayment->amount = $amount;
            $restaurantPayment->status = 'paid';
            $restaurantPayment->payment_date_time = now()->toDateTimeString();
            $restaurantPayment->save();

            // Fetch the restaurant details
            $restaurant = Restaurant::find($restaurantPayment->restaurant_id);
            $restaurant->package_id = $restaurantPayment->package_id;
            $restaurant->package_type = $restaurantPayment->package_type;
            $restaurant->trial_ends_at = null;
            $restaurant->is_active = true;
            $restaurant->status = 'active';
            $restaurant->license_expire_on = null;
            $restaurant->license_updated_at = now();
            $restaurant->subscription_updated_at = now();
            $restaurant->save();
            // Deactivate existing subscriptions
            GlobalSubscription::where('restaurant_id', $restaurant->id)
                ->where('subscription_status', 'active')
                ->update(['subscription_status' => 'inactive']);

            // Create new Subscription entry
            $subscription = new GlobalSubscription();
            $subscription->transaction_id = $transactionId;
            $subscription->restaurant_id = $restaurant->id;
            $subscription->package_type = $restaurant->package_type;
            $subscription->currency_id = $restaurantPayment->currency_id;
            $subscription->quantity = 1;
            $subscription->package_id = $restaurant->package_id;
            $subscription->gateway_name = 'flutterwave';
            $subscription->subscription_status = 'active';
            $subscription->flutterwave_id = $FlutterwaveTransactionId;
            $subscription->flutterwave_payment_ref = $transactionRef;
            $subscription->flutterwave_status = $responseData['data']['status'] ?? null;
            $subscription->flutterwave_customer_id = $customerId;
            $subscription->subscription_id = $subscriptionId;
            $subscription->ends_at = $restaurant->license_expire_on ?? null;
            $subscription->subscribed_on_date = now()->format('Y-m-d H:i:s');
            $subscription->save();

            $package = Package::find($restaurantPayment->package_id);

            if (!$package) {
                return redirect()->route('dashboard')->with([
                    'flash.banner' => __('messages.packageNotFound'),
                    'flash.bannerStyle' => 'danger'
                ]);
            }
            // Check if invoice exists, otherwise create/update
            if ($subscription) {
                $invoice = GlobalInvoice::updateOrCreate(
                    ['transaction_id' => $subscription->transaction_id],
                    [
                        'restaurant_id' => $restaurant->id,
                        'currency_id' => $subscription->currency_id,
                        'package_id' => $subscription->package_id,
                        'global_subscription_id' => $subscription->id,
                        'package_type' => $subscription->package_type,
                        'total' => $amount, // Store amount
                        'plan_id' => match ($restaurantPayment->package_type) {
                            'lifetime' => null,
                            'annual' => $package->flutterwave_annual_plan_id,
                            'monthly' => $package->flutterwave_monthly_plan_id,
                            default => null
                        },
                        'invoice_id' => $transactionRef,
                        'gateway_name' => 'flutterwave',
                        'status' => 'active',
                    ]
                );

                if (!$invoice->pay_date) {
                    $invoice->pay_date = now();
                }

                $nextPayDate = $subscription->package_type === 'monthly'
                    ? Carbon::parse($invoice->pay_date)->addMonth()
                    : Carbon::parse($invoice->pay_date)->addYear();

                $invoice->next_pay_date = $nextPayDate;
                $invoice->save();
            }

            $emailSetting = EmailSetting::first();

            if ($emailSetting->mail_driver === 'smtp' && $emailSetting->verified) {
                $generatedBy = User::withoutGlobalScopes()->whereNull('restaurant_id')->first();
                Notification::send($generatedBy, new RestaurantUpdatedPlan($restaurant, $subscription->package_id));

                // Notify restaurant admin
                $restaurantAdmin = $restaurant->restaurantAdmin($restaurant);
                Notification::send($restaurantAdmin, new restaurantUpdatedPlan($restaurant, $subscription->package_id));
            }

            session()->forget('restaurant');
            session()->flash('flash.banner', __('messages.planUpgraded'));
            session()->flash('flash.bannerStyle', 'success');
            session()->flash('flash.link', route('settings.index', ['tab' => 'billing']));

            return redirect()->route('dashboard')->with('livewire', true);

        } catch (\Exception $e) {
            \Log::error('Flutterwave payment error: ' . $e);
            return redirect()->route('dashboard')->with([
                'flash.banner' => __('messages.FlutterwavePaymentError', ['message' => $e->getMessage()]),
                'flash.bannerStyle' => 'danger'
            ]);
        }
    }
}
