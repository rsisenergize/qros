<div>
    <div
        class="mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.restaurantInformation')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.generalHelp')</x-help-text>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <form wire:submit="submitForm">
                    <div>
                        <div>
                            <x-label class="mt-4" for="restaurantName"
                                value="{{ __('modules.settings.restaurantName') }}" />
                            <x-input id="restaurantName" class="block mt-2 w-full" type="text"
                                placeholder="{{ __('placeholders.restaurantNamePlaceHolder') }}" autofocus
                                wire:model='restaurantName' />
                            <x-input-error for="restaurantName" class="mt-2" />
                        </div>

                        <div>
                            <x-label class="mt-4" for="restaurantPhoneNumber"
                                value="{{ __('modules.settings.restaurantPhoneNumber') }}" />
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

                            <x-input-error for="restaurantPhoneCode" class="mt-2" />
                            <x-input-error for="restaurantPhoneNumber" class="mt-2" />
                        </div>



                        <div>
                            <x-label class="mt-4" for="restaurantEmailAddress"
                                value="{{ __('modules.settings.restaurantEmailAddress') }}" />
                            <x-input id="restaurantEmailAddress" class="block mt-2 w-full" type="email"
                                wire:model='restaurantEmailAddress' />
                            <x-input-error for="restaurantEmailAddress" class="mt-2" />
                        </div>

                        <div>
                            <x-label class="mt-4" for="restaurantAddress"
                                value="{{ __('modules.settings.restaurantAddress') }}" />
                            <x-textarea class="block mt-2 w-full" wire:model='restaurantAddress' rows='3' />
                            <x-input-error for="restaurantAddress" class="mt-2" />
                        </div>
                    </div>
                    <div class="col-span-2 mt-3">
                        <x-button>@lang('app.save')</x-button>
                    </div>
                </form>
            </div>

            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <form wire:submit="submitTax" class="flex flex-col justify-between h-full">
                    <div class="rounded-lg p-4 flex-grow">
                        <div class="space-y-4">
                            <x-label for="showTax">
                                <div class="flex items-center cursor-pointer pb-4">
                                    <x-checkbox name="showTax" id="showTax" wire:model.live="showTax" />
                                    <div class="ms-2">
                                        @lang('modules.settings.showTax')
                                    </div>
                                </div>
                            </x-label>

                            @if ($showTax)

                                @foreach ($taxFields as $index => $field)
                                    <div class="flex items-center gap-x-3 justify-between mb-2"
                                        wire:key="main-{{ $index }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-3"
                                            wire:key="data-{{ $index }}">
                                            <div>
                                                <x-label for="taxName{{ $index }}"
                                                    value=" {{ __('modules.settings.taxName') }}" />
                                                <x-input id="taxName{{ $index }}" class="block mt-1 w-full"
                                                    type="text" required
                                                    wire:model="taxFields.{{ $index }}.taxName" />
                                                <x-input-error for="taxFields.{{ $index }}.taxName"
                                                    class="mt-2" />
                                            </div>
                                            <div>
                                                <x-label for="taxId{{ $index }}"
                                                    value="{{ __('modules.settings.taxId') }}" />
                                                <x-input id="taxId{{ $index }}" class="block mt-1 w-full" required
                                                    type="text" wire:model="taxFields.{{ $index }}.taxId" />
                                                <x-input-error for="taxFields.{{ $index }}.taxId"
                                                    class="mt-2" />
                                            </div>
                                        </div>

                                        <x-secondary-button type="button"
                                            wire:click="showConfirmationField({{ $field['id'] ?? 'null' }}, {{ $index }})"
                                            class="mt-5 p-2 {{ $index > 0 ? 'visible' : 'invisible' }}">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5 text-red-500">
                                                <path d="M10 11V17" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M14 11V17" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M4 7H20" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path
                                                    d="M6 7H12H18V18C18 19.6569 16.6569 21 15 21H9C7.34315 21 6 19.6569 6 18V7Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path
                                                    d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7H9V5Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </svg>
                                        </x-secondary-button>

                                    </div>
                                @endforeach
                                 <x-secondary-button type="button" class="m-2" wire:click="addMoreTaxFields" name="addMore">
                                    @lang('modules.settings.addMore')
                                </x-secondary-button>
                            @else
                                <div class="flex flex-col items-center justify-center p-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-12 h-12 mt-5 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 8.25h15m-15 0V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V8.25m-15 0l1.5-3.75A2.25 2.25 0 019 3.75h6a2.25 2.25 0 012.25 1.5l1.5 3.75M12 11.25v6.75m-3-3h6" />
                                    </svg>
                                    <p class="mt-4 text-lg text-center text-gray-500">
                                        @lang('modules.settings.noTaxFound')
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="flex justify-end mt-4 pt-4">

                        <x-button class="m-2">@lang('app.saveTax')</x-button>
                    </div>
                </form>
            </div>

            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4" wire:key='charges-section'>
                @if(!$showChargesForm)
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">@lang('modules.settings.charges')</h3>
                    <x-button type='button' wire:click="showForm">@lang('modules.settings.addCharge')</x-button>
                </div>

                <div class="flex flex-col">

                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow">
                                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.settings.chargeName')
                                            </th>

                                            <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.settings.chargeType')
                                            </th>

                                            <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.settings.rate')
                                            </th>

                                            <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                @lang('modules.settings.orderType')
                                            </th>
                                            <th scope="col" class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                                @lang('app.action')
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                                        @forelse ($charges as $item)
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center">
                                                    {{ $item->charge_name }}
                                                    <span class="inline-flex items-center ml-2 px-1.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_enabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                        <span class="w-1.5 h-1.5 mr-1 rounded-full {{ $item->is_enabled ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                                        {{ $item->is_enabled ? __('app.active') : __('app.inactive') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ ucfirst($item->charge_type) }}
                                            </td>
                                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $item->charge_type == 'percent' ? $item->charge_value . '%' : currency_format($item->charge_value, restaurant()->currency_id) }}
                                            </td>

                                            <td class="py-2.5 px-4 text-base text-gray-900 dark:text-white">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($item->order_types as $orderTypes)
                                                        @if ($orderTypes)
                                                            <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                                                {{ Str::title(str_replace('_', ' ', $orderTypes)) }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>

                                            <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                                <x-secondary-button wire:click='showForm({{ $item->id }})'
                                                    wire:key='charge-edit-{{ $item->id . microtime() }}'>
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z" clip-rule="evenodd"/></svg>
                                                </x-secondary-button>

                                                <x-danger-button-table wire:click="confirmDeleteCharge({{ $item->id }})"
                                                    wire:key='charge-del-{{ $item->id . microtime() }}'>
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2zM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1" clip-rule="evenodd"/></svg>
                                                </x-danger-button-table>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 text-center text-gray-500 dark:text-gray-400">
                                            <td class="py-2.5 px-4 space-x-6" colspan="6">
                                                @lang('messages.noChargeFound')
                                            </td>
                                        </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                            <div class="p-2">{{ $charges->links() }}</div>
                    </div>
                </div>
                </div>
                @else
                @livewire('forms.addCharges', ['selectedChargeId' => $selectedChargeId])
                @endif
            </div>
            <div class="p-4 rounded-lg border dark:text-gray-200 border-gray-200 dark:border-gray-600" wire:key='predefined-amounts-section'>
                @if(!$showPredefinedAmountsForm)
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">@lang('modules.settings.predefinedAmounts')</h3>
                    <x-button type='button' wire:click="editPredefinedAmounts">@lang('modules.settings.editAmounts')</x-button>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    @foreach($predefinedAmounts as $amount)
                    <div class="p-3 text-center rounded-lg border border-gray-300 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <span class="font-medium">{{ currency_format($amount['amount']) }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-500 dark:text-gray-400">@lang('modules.settings.editPredefinedAmounts')</h3>
                        <x-button-cancel type='button' wire:click="hidePredefinedAmountsForm">@lang('app.cancel')</x-button-cancel>
                    </div>

                    <x-input-error for="predefinedAmounts" class="mt-1" />

                    <div class="space-y-3">
                        @foreach($predefinedAmounts as $index => $amount)
                        <div class="flex gap-3 items-center" wire:key="amount-{{ $index }}">
                            <div class="flex-1">
                                <x-input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="predefinedAmounts.{{ $index }}.amount"
                                    class="w-full"
                                    placeholder="{{__('modules.settings.enterAmount')}}"
                                />
                                <x-input-error for="predefinedAmounts.{{ $index }}.amount" class="mt-1" />
                            </div>

                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between">

                        <x-button wire:click="savePredefinedAmounts">
                            @lang('app.save')
                        </x-button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal wire:model="confirmDeleteChargeModal">
        <x-slot name="title">
            @lang('modules.settings.deleteCharge')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteChargeMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteChargeModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($selectedChargeId)
            <x-danger-button class="ml-3" wire:click='deleteCharge({{ $selectedChargeId }})' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmDeleteTaxModal">
        <x-slot name="title">
            @lang('modules.settings.deleteTax')?
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteTaxMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteTaxModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteAndRemove({{ $fieldId }} , {{ $fieldIndex }} )"
                wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
