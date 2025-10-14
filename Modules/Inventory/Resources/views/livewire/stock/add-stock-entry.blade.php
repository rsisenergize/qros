<div>

    <!-- Form Header -->
    <div class="mb-8">
   
        <p class="text-sm text-gray-600 dark:text-gray-400">
            @lang("inventory::modules.stock.addStockEntryDescription")
        </p>
    </div>

    <!-- Form -->
    <form wire:submit="submitForm" class="space-y-8">
        <!-- Transaction Type -->
        <div class=" p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>@lang("inventory::modules.stock.transactionType")</span>
                </div>
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <input type="radio" id="typeIn" name="transactionType" value="IN" class="hidden peer" wire:model.live='transactionType' />
                    <label for="typeIn"  class="flex flex-col items-center space-y-2 justify-center p-4 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium">
                        <svg class="w-6 h-6 text-green-500 peer-checked:text-indigo-600"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-white peer-checked:text-indigo-600">
                            @lang("inventory::modules.stock.stockIn")
                        </span>
                    </label>
                </div>

                <div>
                    <input type="radio" wire:model.live="transactionType" value="OUT" id="typeOut" class="hidden peer" wire:model='transactionType' />
                    <label class="flex flex-col items-center space-y-2 justify-center p-4 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium" for="typeOut">
                        <svg class="w-6 h-6 text-red-500 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-white peer-checked:text-indigo-600">@lang("inventory::modules.stock.stockOut")</span>
                    </label>
                </div>

                <div>
                    <input type="radio" wire:model.live="transactionType" value="WASTE" id="typeWaste" class="hidden peer" wire:model='transactionType' />
                    <label class="flex flex-col items-center space-y-2 justify-center p-4 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium" for="typeWaste">
                        <svg class="w-6 h-6 text-yellow-500 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-white peer-checked:text-indigo-600">@lang("inventory::modules.stock.waste")</span>
                    </label>
                </div>

                <div>
                    <input type="radio" wire:model.live="transactionType" value="TRANSFER" id="typeTransfer" class="hidden peer" wire:model='transactionType' />
                    <label class="flex flex-col items-center space-y-2 justify-center p-4 text-gray-600 bg-white border-2 border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-gray-900 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium" for="typeTransfer">
                        <svg class="w-6 h-6 text-blue-500 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-white peer-checked:text-indigo-600">@lang("inventory::modules.stock.transfer")</span>
                    </label>
                </div>

       
            </div>
        </div>

        <div class="grid grid-cols-1  gap-8">
            <!-- Item Selection -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        {{ __('inventory::modules.inventoryItem.name') }}
                    </div>
                </label>
                <div class="mt-1">
                    <livewire:inventory::components.searchable-select
                        :name="'item-search'"
                        :placeholder="__('inventory::modules.stock.searchItems')"
                        :items="$inventoryItems"
                        :model-id="$inventoryItem"
                        :display-field="'name'"
                        :sub-field="'category.name'"
                        :dispatch-event="'item-selected'"
                    />
                </div>
                @error('inventoryItem')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                        <span>@lang("inventory::modules.stock.quantity")</span>
                    </div>
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" 
                        wire:model="quantity"
                        id="quantity"
                        step="0.01"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
                    />
                    @error('quantity')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror

                    <div class="absolute inset-y-0 right-0 pr-10 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm" id="price-currency">
                            {{ $inventoryItem ? $inventoryItems->find($inventoryItem)->unit->symbol : '' }}
                        </span>
                    </div>
                </div>
            </div>


            @if ($transactionType == 'IN')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Supplier Selection -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <label for="supplier-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <div class="flex items-center space-x-2">
                                {{ __('inventory::modules.stock.unitPurchasePrice') }}
                            </div>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" 
                                wire:model="unitPurchasePrice"
                                id="unitPurchasePrice"
                                step="0.01"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
                                />
                            @error('unitPurchasePrice')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror

                            <div class="absolute inset-y-0 right-0 pr-14 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm" id="price-currency">
                                    {{ restaurant()->currency->currency_code }} / {{ $inventoryItem ? $inventoryItems->find($inventoryItem)->unit->symbol : '' }}
                                </span>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <label for="expiration_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <div class="flex items-center space-x-2">
                                {{ __('inventory::modules.stock.expirationDate') }}
                            </div>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="date" wire:model="expirationDate" id="expiration_date" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                            @error('expirationDate')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif



            @if ($transactionType == 'IN')
                <!-- Supplier Selection -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <label for="supplier-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            {{ __('inventory::modules.stock.selectSupplier') }}
                        </div>
                    </label>
                    <div class="mt-1">
                        <x-select id="supplier"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
                            wire:model="supplier"
                        >
                            <option value="">--</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    @error('supplier')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif


            @if ($transactionType == 'WASTE')
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <label for="waste_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>@lang("inventory::modules.stock.wasteReason")</span>
                    </div>
                </label>
                <select wire:model="wasteReason"
                        id="waste_reason"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                        <option value="">--</option>
                        <option value="expiry">@lang("inventory::modules.stock.expiry")</option>
                        <option value="spoilage">@lang("inventory::modules.stock.spoilage")</option>
                        <option value="customer_complaint">@lang("inventory::modules.stock.customerComplaint")</option>
                        <option value="over_preparation">@lang("inventory::modules.stock.overPreparation")</option>
                        <option value="other">@lang("inventory::modules.stock.other")</option>
                    </select>
                    @error('wasteReason')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            @if ($transactionType == 'TRANSFER')
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>@lang("inventory::modules.stock.selectTargetBranch")</span>
                        </div>
                    </label>
                    <select wire:model.live="branch"
                            id="branch_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                            <option value="">--</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                    </select>
                    @error('branch')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <label for="destination_inventory_item_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>@lang("inventory::modules.stock.targetInventoryItem")</span>
                        </div>
                    </label>
                    <select wire:model.live="destinationInventoryItem"
                            id="destination_inventory_item_id"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                            <option value="">--</option>
                            @foreach ($destinationInventoryItems as $inventoryItem)
                                <option value="{{ $inventoryItem->id }}">{{ $inventoryItem->name }}</option>
                            @endforeach
                    </select>
                    @error('destinationInventoryItem')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                
            @endif


        </div>

        <!-- Form Actions -->
        <div class="flex gap-2">
            <x-button type="submit" >
                @lang("app.save")
            </x-button>

            <x-secondary-button wire:click="$dispatch('hideAddStockEntryModal')">
                @lang("app.cancel")
            </x-secondary-button>
        </div>
    </form>
</div>
