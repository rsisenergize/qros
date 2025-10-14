<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ $movement->item->name }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $movement->item->category->name }}
            </p>
        </div>
        <span @class([
            'px-3 py-1 text-xs font-semibold rounded-full',
            'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' => $movement->transaction_type === 'in',
            'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' => $movement->transaction_type === 'out',
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' => $movement->transaction_type === 'waste',
            'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' => $movement->transaction_type === 'transfer',
        ])>
            {{ __('inventory::modules.movements.types.' . $movement->transaction_type) }}
        </span>
    </div>

    <!-- Form Section -->
    <form wire:submit="update" class="mt-6">
        <div class="space-y-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('inventory::modules.movements.fields.quantity') }}
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" 
                        wire:model="quantity"
                        id="quantity"
                        step="0.01"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
                    >
                    <div class="absolute inset-y-0 right-0 pr-10 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">
                            {{ $movement->item->unit->symbol }}
                        </span>
                    </div>
                </div>
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            @if($movement->transaction_type === 'in')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="unitPurchasePrice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('inventory::modules.stock.unitPurchasePrice') }}
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" 
                                wire:model="unitPurchasePrice"
                                id="unitPurchasePrice"
                                step="0.01"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200"
                            />
                            <div class="absolute inset-y-0 right-0 pr-10 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">
                                    {{ restaurant()->currency->currency_code }} / {{ $movement->item->unit->symbol }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="expirationDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('inventory::modules.stock.expirationDate') }}
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="date" wire:model="expirationDate" id="expirationDate" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                            @error('expirationDate')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Supplier Selection -->
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.movements.fields.supplier') }}
                    </label>
                    <select wire:model="supplier"
                            id="supplier"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                        <option value="">{{ __('inventory::modules.movements.select_supplier') }}</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

               
            @endif

            @if($movement->transaction_type === 'waste')
                <!-- Waste Reason -->
                <div>
                    <label for="wasteReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.movements.fields.waste_reason') }}
                    </label>
                    <select wire:model="wasteReason"
                            id="wasteReason"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                        <option value="">{{ __('inventory::modules.movements.select_reason') }}</option>
                        @foreach(['expiry', 'spoilage', 'customer_complaint', 'over_preparation', 'other'] as $reason)
                            <option value="{{ $reason }}">{{ __('inventory::modules.movements.waste_reasons.' . $reason) }}</option>
                        @endforeach
                    </select>
                    @error('wasteReason')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($movement->transaction_type === 'transfer')
                <!-- Branch Selection -->
                <div>
                    <label for="branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.movements.fields.transfer_branch') }}
                    </label>
                    <select wire:model="branch"
                            id="branch"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-2.5 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors duration-200">
                        <option value="">{{ __('inventory::modules.movements.select_branch') }}</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('branch')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        <!-- Form Actions -->
        <div class="mt-6 flex justify-end space-x-3">
            <x-secondary-button wire:click="$dispatch('hideEditMovementModal')" type="button">
                {{ __('app.cancel') }}
            </x-secondary-button>
            <x-button type="submit">
                {{ __('inventory::modules.movements.update_movement') }}
            </x-button>
        </div>
    </form>
</div> 