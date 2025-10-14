<div class="w-full mx-auto bg-white dark:bg-gray-700 p-6 rounded-lg h-fit shadow-md">

    @if ($showUserForm)
        <form wire:submit="submitForm">
            @csrf

            <h2 class="text-xl font-medium mb-6 mt-3 dark:text-white">@lang('auth.createAccountSignup', ['appName' => global_setting()->name])</h2>
            <div>
                <x-label for="restaurantName" value="{{ __('modules.restaurant.name') }}" />
                <x-input id="restaurantName" class="block mt-1 w-full" type="text" wire:model='restaurantName' />
                <x-input-error for="restaurantName" class="mt-2" />
            </div>

            @includeIf('subdomain::include.register-subdomain')

            <div class="mt-4">
                <x-label for="fullName" value="{{ __('app.fullName') }}" />
                <x-input id="fullName" class="block mt-1 w-full" type="text" wire:model='fullName' />
                <x-input-error for="fullName" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('app.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" wire:model='email' />
                <x-input-error for="email" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-label for="restaurantPhoneNumber" value="{{ __('modules.settings.phone') }}" />

                @if($phoneCodeDetected && $restaurantPhoneCode)
                    <div class="text-xs text-green-600 dark:text-green-400 mb-1">
                        🌍 @lang('messages.phoneCodeDetected', ['code' => '+' . $restaurantPhoneCode])
                    </div>
                @endif

                <div class="flex gap-2 mt-2">
                    <!-- Phone Code Dropdown -->
                    <div x-data="{ isOpen: @entangle('phoneCodeIsOpen').live }" @click.away="isOpen = false" class="relative w-32">
                        <div @click="!{{ $phoneVerified ? 'true' : 'false' }} && (isOpen = !isOpen)"
                            class="p-2 bg-gray-100 border rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600" :class="{ 'cursor-pointer': !{{ $phoneVerified ? 'true' : 'false' }}, 'opacity-50 cursor-not-allowed': {{ $phoneVerified ? 'true' : 'false' }} }">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">
                                    @if($restaurantPhoneCode)
                                        +{{ $restaurantPhoneCode }}
                                    @else
                                        {{ __('modules.settings.select') }}
                                    @endif
                                </span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Search Input and Options -->
                        <ul x-show="isOpen && !{{ $phoneVerified ? 'true' : 'false' }}" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                            <li class="sticky top-0 px-3 py-2 bg-white dark:bg-gray-900 z-10">
                                <x-input wire:model.live.debounce.300ms="phoneCodeSearch" class="block w-full" type="text" placeholder="{{ __('placeholders.search') }}" />
                            </li>
                            @forelse ($phonecodes as $phonecode)
                                <li @click="$wire.selectPhoneCode('{{ $phonecode }}')"
                                    wire:key="phone-code-{{ $phonecode }}"
                                    class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600"
                                    :class="{ 'bg-gray-100 dark:bg-gray-800': '{{ $phonecode }}' === '{{ $restaurantPhoneCode }}' }" role="option">
                                    <div class="flex items-center">
                                        <span class="block ml-3 text-sm whitespace-nowrap">+{{ $phonecode }}</span>
                                        <span x-show="'{{ $phonecode }}' === '{{ $restaurantPhoneCode }}'" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:text-gray-300" x-cloak>
                                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="relative py-2 pl-3 text-gray-500 cursor-default select-none pr-9 dark:text-gray-400">
                                    {{ __('modules.settings.noPhoneCodesFound') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Phone Number Input -->
                    <x-input id="restaurantPhoneNumber" class="block w-full" type="tel"
                        wire:model='restaurantPhoneNumber' placeholder="1234567890" :disabled="$phoneVerified" />
                         <!-- Verify Button (only show if phone verification is enabled and not verified) -->
                    @if($this->isPhoneVerificationEnabled() && !$phoneVerified && !$showOtpField)
                        <x-button type="button" wire:click="sendOtp" wire:loading.attr="disabled" class="whitespace-nowrap">
                            <span wire:loading.remove wire:target="sendOtp">{{ __('sms::modules.restaurant.verify') }}</span>
                            <span wire:loading wire:target="sendOtp">{{ __('sms::modules.restaurant.sending') }}</span>
                        </x-button>
                    @endif
                    <!-- Phone Verified Status -->
                    @if($phoneVerified)
                        <div class="mt-2 flex items-center text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">{{ __('sms::modules.restaurant.verified') }}</span>
                        </div>
                    @endif
                </div>

                <x-input-error for="restaurantPhoneCode" class="mt-2" />
                <x-input-error for="restaurantPhoneNumber" class="mt-2" />
                <x-input-error for="phone_verification" class="mt-2" />
                <x-input-error for="otp_send" class="mt-2" />
                <x-input-error for="phone_verification_required" class="mt-2" />

                <!-- OTP Input Field (show when OTP is sent) -->
                @if($showOtpField && !$phoneVerified)
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-1">
                                    {{ __('sms::modules.restaurant.verificationCodeSent') }}
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mb-3">
                                    {{ __('sms::modules.restaurant.pleaseEnterThe4DigitCodeSentTo') }} +{{ $restaurantPhoneCode }} {{ $restaurantPhoneNumber }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <x-input id="otpCode" class="w-28 text-center text-xl font-bold tracking-[0.5em] bg-white dark:bg-gray-800" type="text" wire:model='otpCode' placeholder="----" maxlength="4" autocomplete="one-time-code" />
                                    </div>

                                    <x-button type="button" wire:click="verifyOtp" wire:loading.attr="disabled" class="whitespace-nowrap bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                                        <span wire:loading.remove wire:target="verifyOtp">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ __('sms::modules.restaurant.verifyCode') }}
                                        </span>
                                        <span wire:loading wire:target="verifyOtp">
                                            <svg class="animate-spin h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('sms::modules.restaurant.verifying') }}
                                        </span>
                                    </x-button>

                                    <button type="button" wire:click="sendOtp" wire:loading.attr="disabled" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline font-medium">
                                        <span wire:loading.remove wire:target="sendOtp">{{ __('sms::modules.restaurant.resendCode') }}</span>
                                        <span wire:loading wire:target="sendOtp">{{ __('sms::modules.restaurant.sending') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-input-error for="otp_verification" class="mt-2" />
                @endif
            </div>



            <div class="mt-4">
                <x-label for="password" value="{{ __('modules.staff.password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" autocomplete="new-password"
                    wire:model='password' />
                <x-input-error for="password" class="mt-2" />
            </div>

            <!-- Terms & Conditions and Privacy Policy Checkbox -->
            @if(global_setting()->show_privacy_consent_checkbox)
            <div class="mt-4">
                <x-label for="termsAndPrivacy">
                    <div class="flex items-center">
                        <x-checkbox name="termsAndPrivacy" id="termsAndPrivacy" wire:model.live="termsAndPrivacy" />
                        <div class="ms-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('I accept the Terms & Conditions and') }}
                                @if(global_setting()->privacy_policy_link)
                                    <a href="{{ global_setting()->privacy_policy_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('Privacy Policy') }}
                                    </a>
                                @else
                                    {{ __('Privacy Policy') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </x-label>
                <x-input-error for="termsAndPrivacy" class="mt-2" />
            </div>

            <!-- Marketing Emails Checkbox -->
            <div class="mt-4">
                <x-label for="marketingEmails">
                    <div class="flex items-center">
                        <x-checkbox name="marketingEmails" id="marketingEmails" wire:model.live="marketingEmails" />
                        <div class="ms-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('I agree to receive marketing emails.') }}
                            </span>
                        </div>
                    </div>
                </x-label>
                <x-input-error for="marketingEmails" class="mt-2" />
            </div>
            @endif


            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' =>
                                        '<a target="_blank" href="' .
                                        route('terms.show') .
                                        '" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">' .
                                        __('Terms of Service') .
                                        '</a>',
                                    'privacy_policy' =>
                                        '<a target="_blank" href="' .
                                        (global_setting()->privacy_policy_link ?: route('policy.show')) .
                                        '" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">' .
                                        __('Privacy Policy') .
                                        '</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="grid items-center grid-cols-1 mt-4 gap-2">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('login') }}">
                    @lang('auth.alreadyRegisteredLoginHere')
                </a>

                <x-button>
                    @lang('modules.restaurant.nextBranchDetails')
                </x-button>
            </div>
        </form>
    @endif

    @if ($showBranchForm)
        <form wire:submit="submitForm2">
            @csrf

            <h2 class="text-xl font-medium mb-6 mt-3 dark:text-white">@lang('modules.restaurant.restaurantBranchDetails')</h2>

            <div>
                <x-label for="branchName" value="{{ __('modules.settings.branchName') }}" />
                <x-input id="branchName" class="block mt-1 w-full" type="text" wire:model='branchName' />
                <x-input-error for="branchName" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-label for="country" value="{{ __('modules.settings.restaurantCountry') }}" />
                <x-select id="restaurantCountry" class="mt-1 block w-full" wire:model.live="country">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}">{{ $item->countries_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="country" class="mt-2" />
            </div>


            <div class="mt-4">
                <x-label for="address" value="{{ __('modules.settings.branchAddress') }}" />
                <x-textarea id="address" class="block mt-1 w-full" rows="3" wire:model='address' />
                <x-input-error for="address" class="mt-2" />
            </div>


            <div class="lg:grid items-center grid-cols-1 mt-4 gap-2">
                @php($target = 'submitForm2')
                <x-button target="submitForm2">
                    {{ __('auth.signup') }}
                </x-button>
            </div>
        </form>
    @endif

</div>
