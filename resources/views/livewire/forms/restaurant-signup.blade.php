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
                        <div @click="isOpen = !isOpen"
                             class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
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
                        <ul x-show="isOpen" x-cloak x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
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
                        wire:model='restaurantPhoneNumber' placeholder="1234567890" />
                </div>

                <x-input-error for="restaurantPhoneNumber" class="mt-2" />
            </div>



            <div class="mt-4">
                <x-label for="password" value="{{ __('modules.staff.password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" autocomplete="new-password"
                    wire:model='password' />
                <x-input-error for="password" class="mt-2" />
            </div>

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
                                        route('policy.show') .
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
