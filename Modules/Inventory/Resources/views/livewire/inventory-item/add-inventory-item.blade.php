<div>

    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-6">
            {{-- Basic Information Section --}}
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-4">
                    {{ __('inventory::modules.inventoryItem.basicInfo') }}
                </h3>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div >
                        <x-label for="name" value="{{ __('inventory::modules.inventoryItem.name') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" 
                            placeholder="{{ __('inventory::placeholders.itemNamePlaceholder') }}" 
                            name="name" wire:model='name' />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="itemCategory" value="{{ __('inventory::modules.inventoryItem.category') }}" />
                        <x-select id="itemCategory" class="block mt-1 w-full" 
                            name="itemCategory" wire:model='itemCategory'>
                            <option value="">{{ __('inventory::placeholders.selectCategory') }}</option>
                            @foreach($itemCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="itemCategory" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="unit" value="{{ __('inventory::modules.inventoryItem.unit') }}" />
                        <x-select id="unit" class="block mt-1 w-full" 
                            name="unit" wire:model.live='unit'>
                            <option value="">{{ __('inventory::placeholders.selectUnit') }}</option>
                            @foreach($units as $itemUnit)
                                <option value="{{ $itemUnit->id }}">{{ $itemUnit->name }} ({{ $itemUnit->symbol }})</option>
                            @endforeach
                        </x-select>
                        <x-input-error for="unit" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="unitPurchasePrice" value="{{ __('inventory::modules.stock.unitPurchasePrice') }}" />
                        <div class="mt-1 relative rounded-md shadow-sm">    
                            <x-input id="unitPurchasePrice" class="block w-full pr-12" type="number" step="0.01" name="unitPurchasePrice" wire:model='unitPurchasePrice' />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm" id="price-currency">
                                    {{ restaurant()->currency->currency_code }}
                                </span>
                            </div>
                        </div>
                        <x-input-error for="unitPurchasePrice" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Threshold Settings Section --}}
            <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 space-y-4">
                <h3 class="text-sm font-medium text-gray-700 mb-4">
                    {{ __('inventory::modules.inventoryItem.thresholdSettings') }}
                </h3>
                
                <div>
                    <x-label for="thresholdQuantity" value="{{ __('inventory::modules.inventoryItem.thresholdQuantity') }}" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <x-input id="thresholdQuantity" 
                            class="block w-full pr-12" 
                            type="number" 
                            step="0.01"
                            placeholder="{{ __('inventory::placeholders.thresholdQuantityPlaceholder') }}" 
                            name="thresholdQuantity" 
                            wire:model='thresholdQuantity' />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="price-currency">
                                {{ $unit ? $units->find($unit)->symbol : '' }}
                            </span>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('inventory::modules.inventoryItem.thresholdHelp') }}
                    </p>
                    <x-input-error for="thresholdQuantity" class="mt-2" />
                </div>

                <div>
                    <label for="supplier-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            {{ __('inventory::modules.inventoryItem.preferredSupplier') }}
                        </div>
                    </label>
                    <div class="mt-1">
                        <livewire:inventory::components.searchable-select
                            :name="'preferredSupplier'"
                            :placeholder="__('inventory::placeholders.selectSupplier')"
                            :items="$suppliers"
                            :model-id="$preferredSupplier"
                            :display-field="'name'"
                            :sub-field="'phone'"
                            :dispatch-event="'preferredSupplier-selected'"
                            wire:model.live="preferredSupplier"
                        />
                    </div>
                    @error('preferredSupplier')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                  
                </div>

                <div>
                    <x-label for="reorderQuantity" value="{{ __('inventory::modules.inventoryItem.reorderQuantity') }}" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <x-input id="reorderQuantity" 
                            class="block w-full pr-12" 
                            type="number" 
                            step="0.01"
                            placeholder="{{ __('inventory::placeholders.reorderQuantityPlaceholder') }}" 
                            name="reorderQuantity" 
                            wire:model='reorderQuantity' />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="price-currency">
                                {{ $unit ? $units->find($unit)->symbol : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
           
        <div class="mt-6 flex space-x-3">

            <x-button type="submit">
                @lang('app.save')
            </x-button>

        <x-secondary-button  type="button" wire:click="$dispatch('hideAddInventoryItem')" >
            @lang('app.cancel')
        </x-secondary-button>
        </div>
    </form>
</div>
