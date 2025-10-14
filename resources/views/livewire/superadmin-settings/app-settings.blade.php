<div
    class="mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">

    <x-cron-message :modal="false" :showModal="false" />

    <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.appSettings')</h3>
    <form wire:submit.prevent="submitForm">
        <div class="grid gap-6">
            <div class="grid lg:grid-cols-3 gap-6">

                <div>
                    <x-label for="appName" value="{{ __('modules.settings.appName') }}" />
                    <x-input id="appName" class="block mt-1 w-full" type="text" autofocus wire:model='appName' />
                    <x-input-error for="appName" class="mt-2" />
                </div>


                <div>
                    <x-label for="defaultLanguage" value="{{ __('modules.settings.defaultLanguage') }}" />
                    <x-select id="defaultLanguage" class="block mt-1 w-full" wire:model='defaultLanguage'>
                        @foreach ($languageSettings as $item)
                            <option value="{{ $item->language_code }}">{{  isset(\App\Models\LanguageSetting::LANGUAGES_TRANS[$item->language_code]) ? \App\Models\LanguageSetting::LANGUAGES_TRANS[$item->language_code] . ' (' . $item->language_name . ')' : $item->language_name }}</option>
                        @endforeach
                    </x-select>

                    <x-input-error for="defaultLanguage" class="mt-2" />
                </div>

                <div>
                    <x-label for="defaultCurrency" value="{{ __('modules.settings.defaultCurrency') }}" />
                    <x-select id="defaultCurrency" class="block mt-1 w-full" wire:model='defaultCurrency'>
                        @foreach ($globalCurrencies as $item)
                            <option value="{{ $item->id }}">{{ $item->currency_name . ' (' . $item->currency_code . ')' }}</option>
                        @endforeach
                    </x-select>

                    <x-input-error for="defaultCurrency" class="mt-2" />
                </div>

                <div>
                    <x-label for="timezone" value="{{ __('modules.settings.timezone') }}" />
                    <div x-data="{ open: false, search: '' }" class="relative">
                        <div @click="open = !open" class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-white dark:bg-gray-900 dark:text-gray-300 cursor-pointer">
                            <div class="flex items-center justify-between p-2">
                                <span x-text="$wire.timezone || '{{ __('modules.settings.selectTimezone') }}'"></span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <div x-show="open" @click.away="open = false" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <div class="p-2 sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-300 dark:border-gray-700">
                                <x-input type="text" x-model="search" class="w-full" placeholder="{{ __('placeholders.search') }}" />
                            </div>
                            <div class="py-1">
                                @foreach ($timezones as $tz)
                                    <div wire:key="tz-{{ $tz }}"
                                         x-show="search === '' || '{{ $tz }}'.toLowerCase().includes(search.toLowerCase())"
                                         @click="$wire.set('timezone', '{{ $tz }}'); open = false"
                                         class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-300">
                                        {{ $tz }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <x-input-error for="timezone" class="mt-2" />
                </div>

                <div>
                    <div class="flex items-center space-x-1">
                        <x-label for="sessionDriver" value="{{ __('modules.settings.sessionDriver') }}" />
                        <svg data-tooltip-target="driver-tooltip-toggle" data-tooltip-placement="top" class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.44 2.44 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/></svg>
                        <div id="driver-tooltip-toggle" role="tooltip"
                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip max-w-64 break-words">
                            @lang('messages.sessionDriverTooltip')
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                    <x-select id="sessionDriver" class="block mt-1 w-full" wire:model='sessionDriver'>
                        <option value="file">@lang('modules.settings.sessionDriverFile')</option>
                        <option value="database">@lang('modules.settings.sessionDriverDatabase')</option>
                    </x-select>
                    <x-input-error for="sessionDriver" class="mt-2" />
                </div>

                <div >
                    <x-label  for="phoneNumber"
                        value="{{ __('modules.settings.phoneNumber') }}" />
                    <div class="flex gap-2 mt-2">
                        <!-- Phone Code Dropdown -->
                        <div x-data="{ isOpen: @entangle('phoneCodeIsOpen').live }" @click.away="isOpen = false" class="relative w-32">
                            <div @click="isOpen = !isOpen"
                                class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">
                                        @if ($phoneCode)
                                            +{{ $phoneCode }}
                                        @else
                                            {{ __('modules.settings.select') }}
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Search Input and Options -->
                            <ul x-show="isOpen" x-transition
                                class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-72 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                <li class="sticky top-0 px-3  bg-white dark:bg-gray-900 z-10">
                                    <x-input wire:model.live.debounce.300ms="phoneCodeSearch" class="block w-full" type="text" placeholder="{{ __('placeholders.search') }}" />
                                </li>
                                @forelse ($phonecodes as $phonecode)
                                    <li @click="$wire.selectPhoneCode('{{ $phonecode }}'); isOpen = false;"
                                        wire:key="phone-code-{{ $phonecode }}"
                                        class="relative py-2 pl-3 pr-9 text-gray-900 transition-colors duration-150 cursor-pointer select-none hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-300"
                                        :class="{ 'bg-gray-100 dark:bg-gray-800': '{{ $phonecode }}' === '{{ $phoneCode }}' }"
                                        role="option">
                                        <div class="flex items-center">
                                            <span class="block ml-3 text-sm whitespace-nowrap">+{{ $phonecode }}</span>
                                            <span x-show="'{{ $phonecode }}' === '{{ $phoneCode }}'" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:text-gray-300" x-cloak>
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="relative py-2 pl-3 pr-9 text-gray-500 cursor-default select-none dark:text-gray-400">
                                        {{ __('modules.settings.noPhoneCodesFound') }}
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Phone Number Input -->
                        <x-input id="phoneNumber" class="block w-full" type="tel"
                            wire:model='phoneNumber' placeholder="1234567890" />
                    </div>

                    <x-input-error for="phoneCode" class="mt-2" />
                    <x-input-error for="phoneNumber" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="mapApiKey" :value="__('modules.delivery.mapApiKey')" />
                <x-input-password id="mapApiKey" class="block mt-1 w-full" type="text" wire:model='mapApiKey' placeholder="{{ __('placeholders.enterGoogleMapApiKey')}}" />
                <x-input-error for="mapApiKey" class="mt-2" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @lang('modules.settings.getGoogleMapApiKeyHelp')
                    <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"
                        class="text-skin-base hover:text-skin-base/[.8] dark:text-skin-base dark:hover:text-skin-base/[.8]">
                        @lang('modules.settings.learnMore')
                    </a>
                </p>
            </div>

            <div>
                <x-label for="showPrivacyConsentCheckbox">
                    <div class="flex items-start space-x-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors duration-200 dark:border-gray-700 dark:hover:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <x-checkbox
                                class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                name="showPrivacyConsentCheckbox"
                                id="showPrivacyConsentCheckbox"
                                wire:model.live='showPrivacyConsentCheckbox'
                            />
                        </div>

                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                @lang('modules.settings.showPrivacyConsentCheckbox')
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.settings.showPrivacyConsentCheckboxHelp')
                            </p>
                        </div>
                    </div>
                </x-label>
            </div>

            <div x-show="$wire.showPrivacyConsentCheckbox" x-transition>
                <x-label for="privacyPolicyLink" :value="__('modules.settings.privacyPolicyLink')" />
                <x-input id="privacyPolicyLink" class="block mt-1 w-full" type="url" wire:model='privacyPolicyLink' placeholder="{{ __('placeholders.enterPrivacyPolicyLink')}}" />
                <x-input-error for="privacyPolicyLink" class="mt-2" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @lang('modules.settings.privacyPolicyLinkHelp')
                </p>
            </div>

            <div >
                <x-label for="requiresApproval">
                    <div class="flex items-start space-x-4 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors duration-200 dark:border-gray-700 dark:hover:bg-gray-700/50">
                        <div class="flex-shrink-0">
                            <x-checkbox
                                class="mt-1 h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                name="requiresApproval"
                                id="requiresApproval"
                                wire:model='requiresApproval'
                            />
                        </div>

                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                @lang('modules.settings.restaurantRequiresApproval')
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @lang('modules.settings.restaurantRequiresApprovalInfo')
                            </p>
                        </div>
                    </div>
                </x-label>
            </div>

            <div>
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>

</div>
