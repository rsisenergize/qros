<div>

<div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.taxSettings')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.taxSettingsDescription')</x-help-text>

        {{-- Tax Settings Tabs --}}
        <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <ul class="flex flex-wrap items-center -mb-px">
                <li class="me-2">
                    <span wire:click="$set('activeTab', 'settings')" @class([
                        'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeTab != 'settings',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'settings',
                    ])>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/></svg>
                        @lang('modules.settings.taxSetting')
                    </span>
                </li>

                <li class="me-2">
                    <span wire:click="$set('activeTab', 'taxes')" @class([
                        'inline-flex items-center gap-x-1 cursor-pointer select-none p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeTab != 'taxes',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => $activeTab == 'taxes',
                    ])>
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @lang('modules.settings.taxTable')
                    </span>
                </li>
            </ul>
        </div>

        @if($activeTab === 'settings')
            <div class="mt-6 space-y-6">
                <form wire:submit="saveTaxSettings">
                    {{-- Tax Mode Setting --}}
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="p-4 space-y-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">@lang('modules.settings.taxMode')</h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach([
                                    ['value' => 'order', 'label' => 'modules.settings.taxModeOrder', 'help' => 'modules.settings.taxModeOrderHelp'],
                                    ['value' => 'item', 'label' => 'modules.settings.taxModeItem', 'help' => 'modules.settings.taxModeItemHelp']
                                ] as $option)
                                    <label @class([
                                        'relative flex flex-col p-3 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md',
                                        'border-skin-base bg-skin-base/10 dark:bg-skin-base/10' => $taxMode === $option['value'],
                                        'border-gray-200 dark:border-gray-700' => $taxMode !== $option['value']
                                    ])>
                                        <div class="flex items-center justify-between mb-2">
                                            <span @class([
                                                'font-medium',
                                                'text-skin-base' => $taxMode === $option['value'],
                                                'text-gray-900 dark:text-white' => $taxMode !== $option['value']
                                            ])>
                                                @lang($option['label'])
                                            </span>
                                            <input type="radio" wire:model.live="taxMode" value="{{ $option['value'] }}" class="w-4 h-4 text-skin-base">
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">@lang($option['help'])</p>
                                    </label>
                                @endforeach
                            </div>

                            @if($taxMode === 'item')
                                <div class="mt-6">
                                    <h4 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">@lang('modules.settings.defaultItemTaxType')</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach([
                                            ['value' => '0', 'label' => 'modules.settings.taxExclusive'],
                                            ['value' => '1', 'label' => 'modules.settings.taxInclusive']
                                        ] as $option)
                                            <label @class([
                                                'relative flex items-center p-2 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md',
                                                'border-skin-base bg-skin-base/10 dark:bg-skin-base/10' => $itemTaxInclusive == $option['value'],
                                                'border-gray-200 dark:border-gray-700' => $itemTaxInclusive != $option['value']
                                            ])>
                                                <input type="radio" wire:model.live="itemTaxInclusive" value="{{ $option['value'] }}" class="w-4 h-4 text-skin-base mr-3">
                                                <span @class([
                                                    'font-medium',
                                                    'text-skin-base' => $itemTaxInclusive == $option['value'],
                                                    'text-gray-900 dark:text-white' => $itemTaxInclusive != $option['value']
                                                ])>
                                                    @lang($option['label'])
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-sm">
                                    <x-checkbox name="assignAllTaxesToItems" id="assignAllTaxesToItems"
                                        wire:model='assignAllTaxesToItems' class="mr-4" />
                                    <div>
                                        <x-label for="assignAllTaxesToItems" :value="__('modules.settings.assignAllTaxesToItems')" class="!mb-1" />
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @lang('modules.settings.assignAllTaxesToItemsDescription')
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-button>
                            <svg class="w-4 h-4 mr-2 inline-flex" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @lang('app.save')
                        </x-button>
                    </div>
                </form>
            </div>

        @elseif($activeTab === 'taxes')
            <div class="mt-6">
                <x-alert type="info" class="mb-0">
                    @lang('messages.taxApplicableInfo')
                </x-alert>
                <div class="flex justify-between items-center mb-4">
                    <x-button type="button" wire:click="showAddCurrency">
                        <svg class="w-4 h-4 mr-1 inline-flex" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        @lang('modules.settings.addTax')
                    </x-button>
                </div>

                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow">
                            <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            @lang('modules.settings.taxName')
                                        </th>

                                        <th scope="col"
                                            class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                            @lang('modules.settings.taxPercent')
                                        </th>

                                        <th scope="col"
                                            class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                            @lang('app.action')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                                    @forelse ($taxes as $item)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                        <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->tax_name }}
                                        </td>

                                        <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->tax_percent }}%
                                        </td>

                                        <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                            <x-secondary-button wire:click='showEditCurrency({{ $item->id }})' wire:key='member-edit-{{ $item->id . microtime() }}'
                                                wire:key='editmenu-item-button-{{ $item->id }}'>
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828"/><path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h4a1 1 0 0 1 0 2H4v10h10v-4a1 1 0 1 1 2 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z" clip-rule="evenodd"/></svg>
                                                @lang('app.update')
                                            </x-secondary-button>

                                            <x-danger-button-table wire:click="showDeleteCurrency({{ $item->id }})"  wire:key='member-del-{{ $item->id . microtime() }}'>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                            </x-danger-button-table>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="py-2.5 px-4 space-x-6" colspan="3">
                                            @lang('messages.noCurrencyFound')
                                        </td>
                                    </tr>
                                    @endforelse

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-right-modal wire:model.live="showEditCurrencyModal">
        <x-slot name="title">
            {{ __("modules.settings.editCurrency") }}
        </x-slot>

        <x-slot name="content">
            @if ($tax)
            @livewire('forms.editTax', ['tax' => $tax], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditCurrencyModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showAddCurrencyModal">
        <x-slot name="title">
            {{ __("modules.settings.addTax") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addTax')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddCurrencyModal', false)" wire:loading.attr="disabled">
                {{ __('app.close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteCurrencyModal">
        <x-slot name="title">
            @lang('modules.settings.deleteTax')
        </x-slot>

        <x-slot name="content">
            @lang('modules.settings.deleteTaxMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteCurrencyModal')" wire:loading.attr="disabled">
                {{ __('app.cancel') }}
            </x-secondary-button>

            @if ($tax)
            <x-danger-button class="ml-3" wire:click='deleteCurrency({{ $tax->id }})' wire:loading.attr="disabled">
                {{ __('app.delete') }}
            </x-danger-button>
            @endif
        </x-slot>
    </x-confirmation-modal>


</div>
