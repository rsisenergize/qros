<div>
    <div class="p-4">
        <h3 class="mb-3 text-xl font-semibold dark:text-white">@lang('modules.settings.customOrderTypes')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.customOrderTypesDescription')</x-help-text>

        <form wire:submit="saveOrderTypes" class="space-y-6">
            {{-- Enable/Disable Custom Order Types Toggle --}}
            <div class="flex gap-x-3 items-center p-4 bg-gray-100 rounded-lg shadow-sm dark:bg-gray-700">
                <x-checkbox name="allowCustomOrderTypeOptions" id="allowCustomOrderTypeOptions"
                    wire:model.live='allowCustomOrderTypeOptions' class="mr-4" />
                <div class="flex-1">
                    <x-label for="allowCustomOrderTypeOptions" :value="__('modules.settings.allowCustomOrderTypeOptions')" class="!mb-1" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.allowCustomOrderTypeOptionsDescription')</p>
                </div>
            </div>

            @if ($allowCustomOrderTypeOptions)
                <div class="mt-6">
                    <div class="mb-4">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm dark:border-gray-700">
                                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                    @lang('modules.settings.orderTypeName')
                                                </th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                    @lang('modules.settings.orderType')
                                                </th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                                    @lang('modules.settings.enabled')
                                                </th>
                                                <th scope="col" class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                                    @lang('app.action')
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='order-types-list'>
                                            @foreach ($orderTypeFields as $index => $field)
                                                @php
                                                    $isDisabled = isset($field['isDefault']) && $field['isDefault'];
                                                @endphp
                                                <tr
                                                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                                    wire:key='order-type-{{ $index }}'
                                                    wire:loading.class.delay='opacity-10'
                                                >
                                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                        <x-input
                                                            id="orderTypeName{{ $index }}"
                                                            @class([
                                                                'w-full',
                                                                $isDisabled ? 'bg-gray-50/80 dark:bg-gray-800/80 opacity-60' : ''
                                                            ])
                                                            type="text"
                                                            wire:model="orderTypeFields.{{ $index }}.orderTypeName"
                                                            :disabled="$isDisabled"
                                                        />
                                                        <x-input-error for="orderTypeFields.{{ $index }}.orderTypeName" class="mt-1" />
                                                    </td>
                                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                        <x-select
                                                            id="orderTypeOption{{ $index }}"
                                                            class="w-full"
                                                            wire:model="orderTypeFields.{{ $index }}.type"
                                                            :disabled="$isDisabled"
                                                        >
                                                            <option value="">{{ __('app.select') }}</option>
                                                            @foreach($orderTypeOptions as $key => $label)
                                                                <option value="{{ $key }}">{{ $label }}</option>
                                                            @endforeach
                                                        </x-select>
                                                        <x-input-error for="orderTypeFields.{{ $index }}.type" class="mt-1" />
                                                    </td>
                                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" wire:model="orderTypeFields.{{ $index }}.enabled" class="sr-only peer">
                                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                        </label>
                                                    </td>
                                                    <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                                        @if (!$isDisabled)
                                                            <x-danger-button-table
                                                                type="button"
                                                                wire:click="showConfirmationOrderTypeField({{ $index }}, {{ $field['id'] ?? 'null' }})"
                                                            >
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                                            </x-danger-button-table>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-4 mt-6">
                        <x-button type="button" wire:click="addMoreOrderTypeFields" wire:loading.attr="disabled" wire:target="addMoreOrderTypeFields" class="inline-flex items-center">
                            <svg wire:loading wire:target="addMoreOrderTypeFields" class="w-4 h-4 mr-1 text-gray-200 animate-spin dark:text-gray-600 fill-skin-base" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                            <svg wire:loading.remove wire:target="addMoreOrderTypeFields" class="w-4 h-4 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            @lang('modules.settings.addMore')
                        </x-button>

                        <x-button type="submit" wire:loading.attr="disabled" wire:target="saveOrderTypes" class="inline-flex gap-x-2 items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                wire:loading.remove wire:target="saveOrderTypes">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg aria-hidden="true" wire:loading wire:target="saveOrderTypes"
                                class="w-4 h-4 text-gray-200 animate-spin dark:text-gray-600 fill-skin-base mr-1"
                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="currentColor" />
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentFill" />
                            </svg>
                            @lang('app.save')
                        </x-button>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center p-8 mt-6 bg-gray-50 rounded-lg border border-gray-200 border-dashed dark:bg-gray-800/50 dark:border-gray-700">
                    <svg class="w-12 h-12 text-gray-400" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="4" width="14" height="17" rx="2" stroke="currentColor" stroke-width="2"/><path d="M9 9h6m-6 4h6m-6 4h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <p class="mt-4 text-lg font-medium text-gray-500 dark:text-gray-400">
                        @lang('modules.settings.noOrderTypesFound')
                    </p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        @lang('modules.settings.allowCustomOrderTypeOptionsDescription')
                    </p>
                </div>
            @endif
        </form>
    </div>

    {{-- Delete Confirmation Modal --}}
    <x-confirmation-modal wire:model="confirmDeleteOrderTypeModal">
        <x-slot name="title">
            @lang('modules.settings.deleteOrderType')
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteOrderTypeConfirm')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmDeleteOrderTypeModal', false)" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteAndRemove({{ $fieldId }}, {{ $fieldIndex }})"
                wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
