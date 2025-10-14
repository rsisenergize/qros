<div>
    <x-modal wire:model="showModal" maxWidth="4xl">
        <div class="p-6">
            <div class="text-lg font-medium text-gray-900 mb-6">
                {{ trans('inventory::modules.purchaseOrder.receive_title') }} #{{ $purchaseOrder->po_number ?? '' }}
            </div>

            <form wire:submit.prevent="receive">
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ trans('inventory::modules.inventoryItem.name') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ trans('inventory::modules.purchaseOrder.ordered_quantity') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ trans('inventory::modules.purchaseOrder.previously_received') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ trans('inventory::modules.purchaseOrder.receiving_quantity') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ trans('inventory::modules.purchaseOrder.remaining') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($item['quantity'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($item['received_quantity'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-input type="number" 
                                                wire:model="items.{{ $index }}.receiving_quantity"
                                                step="0.01" 
                                                min="0" 
                                                {{-- max="{{ $item['quantity'] - $item['received_quantity'] }}" --}}
                                                class="block w-32" />
                                        @error("items.{$index}.receiving_quantity")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($item['quantity'] - $item['received_quantity'] - ($items[$index]['receiving_quantity'] ?? 0), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end space-x-3">
                    <x-secondary-button wire:click="$set('showModal', false)" wire:loading.attr="disabled">
                        {{ trans('app.cancel') }}
                    </x-secondary-button>

                    <x-button type="submit" wire:loading.attr="disabled">
                        {{ trans('inventory::modules.purchaseOrder.receive_items') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</div> 