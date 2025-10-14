<div>
    <x-dialog-modal wire:model.live="showSignupModal"  maxWidth="md" maxHeight="full">
        <x-slot name="title">
            <h2 class="text-lg">
                @lang($showSignUpProcess ? 'auth.signup' : 'app.login')
            </h2>
        </x-slot>

        <x-slot name="content">

            @if (!$showVerifcationCode)
            <form wire:submit="submitForm">
                @csrf
                <div class="space-y-4">

                    @if ($useSmsLogin)
                        <!-- Phone fields shown when SMS login is enabled -->
                        <div>
                            <x-label for="customerPhone" value="{{ __('modules.customer.phone') }}" />
                            <div class="flex gap-2 mt-2">
                                <!-- Phone Code Dropdown -->
                                <div x-data="{ isOpen: @entangle('phoneCodeIsOpen').live }" @click.away="isOpen = false" class="relative w-32">
                                    <div @click="isOpen = !isOpen"
                                        class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm">
                                                @if($phoneCode)
                                                    +{{ $phoneCode }}
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
                                    <ul x-show="isOpen" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                        <li class="sticky top-0 px-3 py-2 bg-white dark:bg-gray-900 z-10">
                                            <x-input wire:model.live.debounce.300ms="phoneCodeSearch" class="block w-full" type="text" placeholder="{{ __('placeholders.search') }}" />
                                        </li>
                                        @forelse ($phonecodes ?? [] as $phonecode)
                                            <li @click="$wire.selectPhoneCode('{{ $phonecode }}')"
                                                wire:key="phone-code-{{ $phonecode }}"
                                                class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600"
                                                :class="{ 'bg-gray-100 dark:bg-gray-800': '{{ $phonecode }}' === '{{ $phoneCode }}' }" role="option">
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
                                            <li class="relative py-2 pl-3 text-gray-500 cursor-default select-none pr-9 dark:text-gray-400">
                                                {{ __('modules.settings.noPhoneCodesFound') }}
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>

                                <!-- Phone Number Input -->
                                <x-input id="customerPhone" class="block w-full" type="tel" wire:model='phone' required />
                            </div>
                            <x-input-error for="phoneCode" class="mt-2" />
                            <x-input-error for="phone" class="mt-2" />
                        </div>
                    @else
                        <!-- Email field shown when SMS login is disabled -->
                        <div>
                            <x-label for="customerEmail" value="{{ __('app.email') }}" />
                            <x-input id="customerEmail" class="block mt-1 w-full" type="email" wire:model='email' />
                            <x-input-error for="email" class="mt-2" />
                        </div>
                    @endif

                    @if ($showSignUpProcess)
                    <div>
                        <x-label for="customerName" value="{{ __('app.name') }}" />
                        <x-input id="customerName" class="block mt-1 w-full" type="text" wire:model='name' />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    @if ($useSmsLogin)
                        <!-- Email field shown when SMS login is disabled -->
                        <div>
                            <x-label for="customerEmail" value="{{ __('app.email') }}" />
                            <x-input id="customerEmail" class="block mt-1 w-full" type="email" wire:model='email' />
                            <x-input-error for="email" class="mt-2" />
                        </div>
                    @else
                        <div>
                            <x-label for="customerPhone" value="{{ __('modules.customer.phone') }}" />
                            <div class="flex gap-2 mt-2">
                                <!-- Phone Code Dropdown -->
                                <div x-data="{ isOpen: @entangle('phoneCodeIsOpen').live }" @click.away="isOpen = false" class="relative w-32">
                                    <div @click="isOpen = !isOpen"
                                        class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm">
                                                @if($phoneCode)
                                                    +{{ $phoneCode }}
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
                                    <ul x-show="isOpen" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                        <li class="sticky top-0 px-3 py-2 bg-white dark:bg-gray-900 z-10">
                                            <x-input wire:model.live.debounce.300ms="phoneCodeSearch" class="block w-full" type="text" placeholder="{{ __('placeholders.search') }}" />
                                        </li>
                                        @forelse ($phonecodes ?? [] as $phonecode)
                                            <li @click="$wire.selectPhoneCode('{{ $phonecode }}')"
                                                wire:key="phone-code-{{ $phonecode }}"
                                                class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600"
                                                :class="{ 'bg-gray-100 dark:bg-gray-800': '{{ $phonecode }}' === '{{ $phoneCode }}' }" role="option">
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
                                            <li class="relative py-2 pl-3 text-gray-500 cursor-default select-none pr-9 dark:text-gray-400">
                                                {{ __('modules.settings.noPhoneCodesFound') }}
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>

                                <!-- Phone Number Input -->
                                <x-input id="customerPhone" class="block w-full" type="tel" wire:model='phone' required />
                            </div>
                            <x-input-error for="phoneCode" class="mt-2" />
                            <x-input-error for="phone" class="mt-2" />
                        </div>
                        @endif
                    @endif

                </div>

                <div class="flex justify-between w-full pb-4 space-x-4 mt-6">
                    <x-button>@lang('app.continue')</x-button>
                    <x-button-cancel  wire:click="$toggle('showSignupModal')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
            @endif


            @if ($showVerifcationCode)
            <form wire:submit="submitVerification">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-label for="verificationCode" value="{{ __('app.verificationCode') }}" />
                        <x-input id="verificationCode" class="block mt-1 w-full" type="tel" wire:model='verificationCode' />
                        <x-input-error for="verificationCode" class="mt-2" />
                    </div>

                    <div>
                        <a href="javascript:;" class="text-sm underline underline-offset-2 text-gray-600" wire:click='sendVerification'>@lang('app.resendVerificatonCode')</a>
                    </div>
                </div>

                <div class="flex justify-between w-full pb-4 space-x-4 mt-6">
                    <x-button>@lang('app.continue')</x-button>
                    <x-button-cancel  wire:click="$toggle('showSignupModal')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
                </div>
            </form>
            @endif

        </x-slot>

    </x-dialog-modal>
</div>
