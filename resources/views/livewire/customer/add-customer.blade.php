<x-dialog-modal wire:model.live="showAddCustomerModal" maxWidth="2xl">
    <x-slot name="title">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-skin-base rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">@lang('modules.customer.addCustomer')</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.customer.searchOrCreate')</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <form wire:submit="submitForm">
            @csrf
            <div class="space-y-4">
                <!-- Search Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.customer.searchCustomer')</h3>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            class="block w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="@lang('modules.customer.searchPlaceholder')"
                            autofocus
                        />
                    </div>

                    <!-- Search Results Dropdown -->
                    <div class="relative mt-3" @click.away="$wire.call('resetSearch')">
                        @if($availableResults && count($availableResults) > 0)
                            <div class="absolute z-50 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                                <div class="max-h-60 overflow-y-auto">
                                    <div class="p-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                            @lang('modules.customer.foundCustomers', ['count' => count($availableResults)])
                                        </p>
                                    </div>
                                    @foreach($availableResults as $result)
                                        <div wire:key="customer-{{ $result->id }}"
                                             wire:click="selectCustomer({{ $result->id }})"
                                             class="group flex items-center p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                            <div class="flex-shrink-0 ltr:mr-3 rtl:ml-3">
                                                <div class="w-8 h-8 rounded-full bg-skin-base flex items-center justify-center">
                                                    <span class="text-white font-medium text-sm">{{ strtoupper(substr($result->name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                    {{ $result->name }}
                                                </p>
                                                <div class="flex flex-wrap gap-3">
                                                    @if($result->phone)
                                                        <span class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                                                            <svg class="w-3 h-3 ltr:mr-1 rtl:ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                            </svg>
                                                            {{ $result->phone }}
                                                        </span>
                                                    @endif
                                                    @if($result->email)
                                                        <span class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                                                            <svg class="w-3 h-3 ltr:mr-1 rtl:ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                            </svg>
                                                            {{ $result->email }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($searchQuery && strlen($searchQuery) >= 2)
                            <div class="absolute z-50 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                                <div class="p-4 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">@lang('modules.customer.noCustomersFound')</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">@lang('modules.customer.noCustomersMatching', ['query' => $searchQuery])</p>
                                    <button type="button"
                                            wire:click="createNewCustomer"
                                            class="inline-flex items-center px-3 py-2 bg-skin-base hover:bg-skin-base/80 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        @lang('modules.customer.createNewCustomer')
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Details Form -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white">@lang('modules.customer.customerDetails')</h3>
                        </div>
                        @if($selectedCustomerId)
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="w-3 h-3 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    @lang('modules.customer.existingCustomer')
                                </span>
                                <button type="button"
                                        wire:click="clearSelection"
                                        class="text-xs text-skin-base hover:text-skin-base/80 dark:text-skin-base/40 dark:hover:text-skin-base/30 transition-colors">
                                    @lang('modules.customer.createNewInstead')
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Name Field -->
                        <div class="space-y-1">
                            <x-label for="customerName" value="{{ __('modules.customer.name') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                            <div class="relative">
                                <input id="customerName"
                                       type="text"
                                       name="customerName"
                                       wire:model='customerName'
                                       placeholder="@lang('modules.customer.enterCustomerName')"
                                       {{ $selectedCustomerId && !$editingFields['name'] ? 'readonly' : '' }}
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $selectedCustomerId && !$editingFields['name'] ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : '' }}" />
                                @if($selectedCustomerId)
                                    <button type="button"
                                            wire:click="toggleFieldEdit('name')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                        @if($editingFields['name'])
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        @endif
                                    </button>
                                @endif
                            </div>
                            <x-input-error for="customerName" class="text-xs" />
                        </div>

                        <!-- Phone Field -->
                        <div class="space-y-1">
                            <x-label for="customerPhone" value="{{ __('modules.customer.phone') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                            
                            <div class="flex gap-2">
                                <!-- Phone Code Dropdown -->
                                <div x-data="{ isOpen: @entangle('phoneCodeIsOpen').live }" @click.away="isOpen = false" class="relative w-32">
                                    <div @click="isOpen = !isOpen"
                                        class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600 {{ $selectedCustomerId && !$editingFields['phone'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm">
                                                @if($customerPhoneCode)
                                                    +{{ $customerPhoneCode }}
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
                                            <input wire:model.live.debounce.300ms="phoneCodeSearch" 
                                                   class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                                   type="text" 
                                                   placeholder="{{ __('placeholders.search') }}" />
                                        </li>
                                        @forelse ($phonecodes as $phonecode)
                                            <li @click="$wire.selectPhoneCode('{{ $phonecode }}')"
                                                wire:key="phone-code-{{ $phonecode }}"
                                                class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600"
                                                :class="{ 'bg-gray-100 dark:bg-gray-800': '{{ $phonecode }}' === '{{ $customerPhoneCode }}' }" role="option">
                                                <div class="flex items-center">
                                                    <span class="block ml-3 text-sm whitespace-nowrap">+{{ $phonecode }}</span>
                                                    <span x-show="'{{ $phonecode }}' === '{{ $customerPhoneCode }}'" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:text-gray-300" x-cloak>
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
                                <div class="flex-1 relative">
                                    <input id="customerPhone"
                                           type="tel"
                                           name="customerPhone"
                                           wire:model='customerPhone'
                                           placeholder="@lang('modules.customer.enterPhoneNumber')"
                                           {{ $selectedCustomerId && !$editingFields['phone'] ? 'readonly' : '' }}
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $selectedCustomerId && !$editingFields['phone'] ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : '' }}" />
                                    @if($selectedCustomerId)
                                        <button type="button"
                                                wire:click="toggleFieldEdit('phone')"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                            @if($editingFields['phone'])
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <x-input-error for="customerPhone" class="text-xs" />
                            <x-input-error for="customerPhoneCode" class="text-xs" />
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="space-y-1">
                        <x-label for="customerEmail" value="{{ __('modules.customer.email') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <div class="relative">
                            <input id="customerEmail"
                                   type="email"
                                   name="customerEmail"
                                   wire:model='customerEmail'
                                   placeholder="@lang('modules.customer.enterEmailAddress')"
                                   {{ $selectedCustomerId && !$editingFields['email'] ? 'readonly' : '' }}
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 {{ $selectedCustomerId && !$editingFields['email'] ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : '' }}" />
                            @if($selectedCustomerId)
                                <button type="button"
                                        wire:click="toggleFieldEdit('email')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                    @if($editingFields['email'])
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    @endif
                                </button>
                            @endif
                        </div>
                        <x-input-error for="customerEmail" class="text-xs" />
                    </div>

                    <!-- Address Field -->
                    <div class="space-y-1">
                        <x-label for="customerAddress" value="{{ __('modules.customer.address') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <div class="relative">
                            <textarea id="customerAddress"
                                      name="customerAddress"
                                      rows="3"
                                      data-gramm="false"
                                      wire:model='customerAddress'
                                      placeholder="@lang('modules.customer.enterDeliveryAddress')"
                                      {{ $selectedCustomerId && !$editingFields['address'] ? 'readonly' : '' }}
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none {{ $selectedCustomerId && !$editingFields['address'] ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : '' }}"></textarea>
                            @if($selectedCustomerId)
                                <button type="button"
                                        wire:click="toggleFieldEdit('address')"
                                        class="absolute top-2 right-2 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                    @if($editingFields['address'])
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    @endif
                                </button>
                            @endif
                        </div>
                        <x-input-error for="customerAddress" class="text-xs" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>@lang('modules.customer.searchOrCreate')</span>
                </div>
                <div class="flex space-x-2 rtl:space-x-reverse">
                    <x-button-cancel wire:click="$set('showAddCustomerModal', false)" class="px-4 py-2 text-sm">
                        @lang('app.cancel')
                    </x-button-cancel>
                    <x-button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-sm flex items-center">
                        <svg class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @lang('app.save')
                    </x-button>
                </div>
            </div>
        </form>
    </x-slot>
</x-dialog-modal>
