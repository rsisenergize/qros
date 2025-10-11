<div>
    <section class="text-gray-700 body-font overflow-hidden p-4 sm:p-6 lg:p-8 border-gray-200 dark:border-gray-700">

        <!-- Header Section - Made responsive -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-2 sm:px-7 py-4">
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                <!-- Monthly Button -->
                <button wire:click="toggle"
                    @class([ 'relative px-4 py-2 text-sm font-medium rounded-full transition-colors duration-200 ease-in-out w-full sm:w-auto'
                    , 'bg-skin-base text-white'=> !$isAnnual,
                    'bg-gray-200 dark:bg-gray-400 text-gray-800' => $isAnnual
                    ])>
                    @lang('modules.billing.monthly')
                </button>

                <!-- Annually Button -->
                <button wire:click="toggle"
                    @class([ 'relative px-4 py-2 text-sm font-medium rounded-full transition-colors duration-200 ease-in-out w-full sm:w-auto'
                    , 'bg-skin-base text-white'=> $isAnnual,
                    'bg-gray-200 dark:bg-gray-400 text-gray-800' => !$isAnnual
                    ])>
                    @lang('modules.billing.annually')
                </button>
            </div>

            <!-- Currency Dropdown with Animation -->
            <div class="w-full sm:w-auto">
                <x-select class="mt-1 block w-full sm:w-auto" wire:model.live="selectedCurrency">
                    @foreach ($currencies as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->currency_name }} ({{ $currency->currency_symbol }})</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <!-- Plans Grid - Responsive design with features in each plan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mx-2 sm:mx-4">
            @foreach ($packages as $package)
                <div @class([
                    'bg-white dark:bg-slate-800 rounded-lg border-2 relative overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300',
                    'border-skin-base ring-2 ring-skin-base/20' => $package->is_recommended,
                    'border-gray-200 dark:border-gray-700' => ! $package->is_recommended
                ])>
                    @if ($package->is_recommended)
                        <div class="bg-skin-base text-white text-center py-2">
                            <span class="text-sm font-semibold tracking-wider">@lang('modules.billing.popular')</span>
                        </div>
                    @endif

                    <!-- Plan Header -->
                    <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $package->package_name }}</h3>
                        
                        <!-- Price -->
                        <div class="mb-4">
                            @if ($package->is_free)
                                <span class="text-3xl font-bold text-skin-base">@lang('modules.billing.free')</span>
                            @else
                                <span class="text-3xl font-bold text-skin-base">
                                    {{ global_currency_format($package->package_type === App\Enums\packageType::LIFETIME ? $package->price : ($isAnnual ? $package->annual_price : $package->monthly_price), $package->currency_id) }}
                                </span>
                            @endif
                        </div>

                        <!-- Plan Type Info -->
                        @if ($package->package_type === App\Enums\packageType::DEFAULT)
                            <div class="flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>@lang('modules.package.defaultPlan')</span>
                                <svg data-popover-target="popover-default-pricing-{{ $package->id }}" data-popover-placement="bottom" class="w-4 h-4 text-gray-400 hover:text-gray-500 cursor-help" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div data-popover id="popover-default-pricing-{{ $package->id }}" role="tooltip" class="absolute text-wrap z-10 invisible inline-block text-sm text-gray-600 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-52 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                                    <div class="p-3 break-words space-y-2">
                                        <p>@lang('modules.package.planExpire')</p>
                                    </div>
                                    <div data-popper-arrow></div>
                                </div>
                            </div>
                        @elseif ($package->package_type === App\Enums\packageType::LIFETIME)
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('modules.billing.lifetimeAccess')</span>
                        @elseif (!$package->is_free)
                            <span class="text-sm text-gray-600 dark:text-gray-400">@lang('modules.billing.billed') {{ $isAnnual ? __('modules.billing.annually') : __('modules.billing.monthly') }}</span>
                        @endif
                    </div>

                    <!-- Features List -->
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 text-center">@lang('landing.features')</h4>
                        
                        @php
                            $packageAllModules = array_merge(
                                $package->modules->pluck('name')->toArray(),
                                $package->additional_features ? json_decode($package->additional_features, true) : []
                            );
                        @endphp

                        <ul class="space-y-3">
                            @foreach ($AllModulesWithFeature as $moduleName)
                                <li class="flex items-start gap-3">
                                    @if (in_array($moduleName, $packageAllModules))
                                        <span class="w-5 h-5 inline-flex items-center justify-center bg-green-500 text-white rounded-full flex-shrink-0 mt-0.5">
                                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" class="w-3 h-3" viewBox="0 0 24 24">
                                                <path d="M20 6L9 17l-5-5"></path>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="w-5 h-5 inline-flex items-center justify-center text-red-500 flex-shrink-0 mt-0.5">
                                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" class="w-3 h-3" viewBox="0 0 24 24">
                                                <path d="M18 6L6 18M6 6l12 12"></path>
                                            </svg>
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('permissions.modules.'.$moduleName) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Action Button -->
                    <div class="p-6 pt-0">
                        @if ($package->is_free || $paymentActive ||
                            ($package->id == $restaurant->package_id && $restaurant->package_type == ($isAnnual ? 'annual' : 'monthly')) ||
                            $package->package_type == App\Enums\PackageType::DEFAULT)
                            <div class="w-full">
                                @if($package->id == $restaurant->package_id && ($restaurant->package_type == ($isAnnual ? 'annual' : 'monthly') || !in_array($restaurant->package_type, ['annual', 'monthly'])))
                                    <button class="w-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 px-4 py-3 rounded-lg font-medium cursor-not-allowed opacity-60 transition-all duration-300">
                                        @lang('modules.package.currentPlan')
                                    </button>
                                @else
                                    <button class="w-full bg-skin-base hover:bg-skin-base/90 text-white px-4 py-3 rounded-lg font-medium transition-all duration-300 group flex items-center justify-center gap-2"
                                        wire:click="selectedPackage({{ $package->id }})">
                                        @lang('modules.package.choosePlan')
                                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 transition-transform duration-500 transform group-hover:translate-x-1" viewBox="0 0 24 24">
                                            <path d="M5 12h14m-7-7 7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-gray-500 dark:text-gray-400 text-sm py-3">
                                @lang('modules.billing.noPaymentOptionEnable')
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Mobile-friendly modal -->
    <x-dialog-modal wire:model.live="showPaymentMethodModal" maxWidth="xl">
        <x-slot name="title">
            @if($free)
                @lang('modules.billing.choosePlan')
            @else
                @lang('modules.billing.choosePaymentMethod')
            @endif
        </x-slot>

        <x-slot name="content">
            @if(!$free)
                <div>
                    @switch($show)
                        @case('payment-method')
                            @include('plans.payment-methods')
                            @break
                        @case('authorize')
                            @include('plans.authorize-payment-method-form')
                            @break
                        @default
                            <!-- Default case if no match -->
                            <p>@lang('modules.billing.noPaymentMethodSelected')</p>
                    @endswitch
                </div>
            @else
                <div class="inline-flex items-baseline text-center text-gray-500">
                    <x-button wire:click="freePlan" class="w-full sm:w-auto">

                        @lang($selectedPlan->packageType === App\Enums\PackageType::DEFAULT ? 'modules.package.choseDefaultPlan' : 'modules.package.choseFreePlan')
                    </x-button>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-0 w-full">
                <x-secondary-button wire:click="$toggle('showPaymentMethodModal')" wire:loading.attr="disabled" class="w-full sm:w-auto order-2 sm:order-1">
                    @lang('app.cancel')
                </x-secondary-button>

                @if($offlineMethodId)
                <x-button class="w-full sm:w-auto order-1 sm:order-2 sm:ml-3" wire:click="{{ $show === 'authorize' ? 'offlinePaymentSubmit' : 'switchPaymentMethod(\'authorize\')' }}" wire:loading.attr="disabled">
                    @lang($show === 'authorize' ? 'app.save' : 'app.select')
                </x-button>
                @endif
            </div>
        </x-slot>
    </x-dialog-modal>

    @if(!$free)

        @if($stripeSettings->razorpay_status == 1 || $stripeSettings->stripe_status == 1 || $stripeSettings->flutterwave_status == 1 || $stripeSettings->paypal_status == 1 || $stripeSettings->payfast_status == 1 || $stripeSettings->paystack_status == 1)
            @push('scripts')
                <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script src="https://checkout.flutterwave.com/v3.js"></script>
                <script src="https://www.paypal.com/sdk/js?client-id={{ $stripeSettings->client_id }}&currency={{ $selectedCurrencyCode }}"></script>
                <script src="https://js.paystack.co/v1/inline.js"></script>
                @script
                    <script>
                        document.addEventListener('livewire:navigated', () => {
                            $wire.on('initiateRazorpay', (jsonData) => {
                                const data = JSON.parse(jsonData);
                                const options = {
                                    key: data.key,
                                    name: data.name,
                                    description: data.description,
                                    image: data.image,
                                    currency: data.currency,
                                    handler: function (response) {
                                        $wire.dispatch('confirmRazorpayPayment', {
                                            payment_id: response.razorpay_payment_id,
                                            reference_id: response.razorpay_subscription_id || response.razorpay_order_id,
                                            signature: response.razorpay_signature,
                                        });
                                    },
                                    notes: data.notes,
                                    modal: {
                                        ondismiss: function() {
                                            if (confirm("Are you sure you want to close the payment form?")) {
                                                console.log("Checkout form closed by the user.");
                                            } else {
                                                console.log("User opted to complete the payment.");
                                            }
                                        }
                                    }
                                };

                                // Set either subscription or order ID appropriately
                                if (data.subscription_id) {
                                    options.subscription_id = data.subscription_id;
                                } else {
                                    options.order_id = data.order_id;
                                    options.amount = data.amount;
                                }

                                var rzp1 = new Razorpay(options);
                                rzp1.on('payment.failed', function(response) {
                                    console.error("Payment failed: ", response);
                                });
                                rzp1.open();
                            });

                            // Stripe payment handling
                            $wire.on('stripePlanPaymentInitiated', (payment) => {
                                document.getElementById('license_payment').value = payment.payment.id;
                                document.getElementById('package_type').value = payment.payment.package_type;
                                document.getElementById('package_id').value = payment.payment.package_id;
                                document.getElementById('currency_id').value = payment.payment.currency_id;
                                document.getElementById('license-payment-form').submit();
                            });

                            $wire.on('redirectToFlutterwave', (params) => {
                                const form = document.getElementById('flutterwavePaymentformNew');
                                const paramsData = params[0].params;

                                // Clear existing inputs (in case of multiple submissions)
                                form.innerHTML = '@csrf'; // Reset form with just CSRF token

                                const addHiddenInput = (name, value) => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = name;
                                    input.value = value;
                                    form.appendChild(input);
                                };

                                console.log('Flutterwave Params:', paramsData);

                                addHiddenInput('payment_id', paramsData.payment_id);
                                addHiddenInput('amount', paramsData.amount);
                                addHiddenInput('currency', paramsData.currency);
                                addHiddenInput('restaurant_id', paramsData.restaurant_id);
                                addHiddenInput('package_id', paramsData.package_id);
                                addHiddenInput('package_type', paramsData.package_type);

                                // Submit the form
                                form.submit();
                            });

                            $wire.on('redirectToPaypal', (params) => {
                                const form = document.getElementById('paypalPaymentForm');
                                const paramsData = params[0].params;

                                // Clear the form and add CSRF token again
                                form.innerHTML = '@csrf';

                                const addHiddenInput = (name, value) => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = name;
                                    input.value = value;
                                    form.appendChild(input);
                                };

                                console.log('PayPal Params:', paramsData);

                                addHiddenInput('payment_id', paramsData.payment_id);
                                addHiddenInput('amount', paramsData.amount);
                                addHiddenInput('currency', paramsData.currency);
                                addHiddenInput('restaurant_id', paramsData.restaurant_id);
                                addHiddenInput('package_id', paramsData.package_id);
                                addHiddenInput('package_type', paramsData.package_type);

                                form.submit();
                            });
                            $wire.on('redirectToPayfast', (params) => {
                                const form = document.getElementById('payfastPaymentForm');
                                const paramsData = params[0].params;

                                // Clear form and retain CSRF token
                                form.innerHTML = '@csrf';

                                const addHiddenInput = (name, value) => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = name;
                                    input.value = value;
                                    form.appendChild(input);
                                };

                                console.log('Payfast Params:', paramsData);

                                addHiddenInput('payment_id', paramsData.payment_id);
                                addHiddenInput('amount', paramsData.amount);
                                addHiddenInput('currency', paramsData.currency);
                                addHiddenInput('restaurant_id', paramsData.restaurant_id);
                                addHiddenInput('package_id', paramsData.package_id);
                                addHiddenInput('package_type', paramsData.package_type);

                                form.submit();
                            });

                            $wire.on('redirectToPaystack', (params) => {
                                const form = document.getElementById('paystackPaymentformNew');
                                const paramsData = params[0].params;

                                // Clear existing inputs (in case of multiple submissions)
                                form.innerHTML = '@csrf'; // Reset form with just CSRF token

                                const addHiddenInput = (name, value) => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = name;
                                    input.value = value;
                                    form.appendChild(input);
                                };

                                addHiddenInput('payment_id', paramsData.payment_id);
                                addHiddenInput('amount', paramsData.amount);
                                addHiddenInput('currency', paramsData.currency);
                                addHiddenInput('restaurant_id', paramsData.restaurant_id);
                                addHiddenInput('package_id', paramsData.package_id);
                                addHiddenInput('package_type', paramsData.package_type);
                                addHiddenInput('email', paramsData.email);

                                form.submit();
                            });
                        });
                    </script>
                @endscript
            @endpush
        @endif
    @endif

</div>


