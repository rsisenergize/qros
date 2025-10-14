<div>
    <x-modal wire:model="showModal" maxWidth="4xl">
        <div class="p-6 bg-white dark:bg-gray-800">
            <div class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                {{ $isEditing ? trans('inventory::modules.purchaseOrder.edit_title') : trans('inventory::modules.purchaseOrder.create_title') }}
            </div>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Supplier -->
                    <div>
                        <x-label for="supplier_id" value="{{ trans('inventory::modules.purchaseOrder.supplier') }}" 
                                class="text-gray-700 dark:text-gray-300" />
                        <select id="supplier_id" wire:model.live="supplierId" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">{{ trans('inventory::modules.purchaseOrder.select_supplier') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="supplierId" class="mt-1" />
                    </div>

                    <!-- Order Date -->
                    <div>
                        <x-label for="order_date" value="{{ trans('inventory::modules.purchaseOrder.order_date') }}"
                                class="text-gray-700 dark:text-gray-300" />
                        <x-input type="date" id="order_date" wire:model="orderDate" 
                                class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        <x-input-error for="orderDate" class="mt-1" />
                    </div>

                    <!-- Expected Delivery Date -->
                    <div>
                        <x-label for="expected_delivery_date" value="{{ trans('inventory::modules.purchaseOrder.expected_delivery_date') }}"
                                class="text-gray-700 dark:text-gray-300" />
                        <x-input type="date" id="expected_delivery_date" wire:model="expectedDeliveryDate" 
                                class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                        <x-input-error for="expectedDeliveryDate" class="mt-1" />
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ trans('inventory::modules.purchaseOrder.items') }}</h3>
                        <x-button type="button" wire:click="addItem">
                            {{ trans('inventory::modules.purchaseOrder.add_item') }}
                        </x-button>
                    </div>

                    <div class="overflow-x-auto overflow-y-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ trans('inventory::modules.inventoryItem.name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ trans('inventory::modules.purchaseOrder.quantity') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ trans('inventory::modules.purchaseOrder.unit_price') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ trans('inventory::modules.purchaseOrder.action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <x-select wire:model.live="items.{{ $index }}.inventoryItemId"
                                                    wire:change="fetchUnitPrice({{ $index }})"
                                                     class="block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                                     required>
                                                <option value="">{{ trans('inventory::modules.purchaseOrder.select_item_placeholder') }}</option>
                                                @foreach($inventoryItems as $item)
                                                    <option value="{{ $item->id }}">{{ $item->display_name }}</option>
                                                @endforeach
                                            </x-select>
                                            @error("items.{$index}.inventoryItemId") <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-input type="number" step="0.01" min="0.01"
                                                    wire:model="items.{{ $index }}.quantity"
                                                    wire:change="calculateSubtotal({{ $index }})"
                                                    class="block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                            @error("items.{$index}.quantity") <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-6 py-4">
                                            <x-input type="number" step="0.01" min="0.01"
                                                    wire:model="items.{{ $index }}.unitPrice"
                                                    wire:change="calculateSubtotal({{ $index }})"
                                                    class="block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" />
                                            @error("items.{$index}.unitPrice") <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-6 py-4">
                                            <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                {{ trans('inventory::modules.purchaseOrder.remove') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <x-label for="notes" value="{{ trans('inventory::modules.purchaseOrder.notes') }}"
                            class="text-gray-700 dark:text-gray-300" />
                    <textarea id="notes" wire:model="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    <x-input-error for="notes" class="mt-1" />
                </div>

                <div class="flex justify-end space-x-3 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <x-secondary-button wire:click="$set('showModal', false)" wire:loading.attr="disabled">
                        {{ trans('app.close') }}
                    </x-secondary-button>

                    <x-button type="submit" wire:loading.attr="disabled">
                        {{ trans('inventory::modules.purchaseOrder.save') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</div> 