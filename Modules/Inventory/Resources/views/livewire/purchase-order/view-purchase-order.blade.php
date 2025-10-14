<div>
    <x-modal wire:model="showModal" maxWidth="4xl">
        @if($purchaseOrder)
        <div class="bg-white dark:bg-gray-800">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ trans('inventory::modules.purchaseOrder.view_title') }}
                    </h3>
                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        {{ $purchaseOrder->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                        {{ $purchaseOrder->status === 'sent' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300' : '' }}
                        {{ $purchaseOrder->status === 'received' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : '' }}
                        {{ $purchaseOrder->status === 'partially_received' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300' : '' }}
                        {{ $purchaseOrder->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300' : '' }}">
                        {{ trans('inventory::modules.purchaseOrder.status.' . $purchaseOrder->status) }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <!-- Order Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ trans('inventory::modules.purchaseOrder.po_number') }}</h4>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $purchaseOrder->po_number }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ trans('inventory::modules.purchaseOrder.supplier') }}</h4>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $purchaseOrder->supplier->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ trans('inventory::modules.purchaseOrder.order_date') }}</h4>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $purchaseOrder->order_date->translatedFormat('M d, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ trans('inventory::modules.purchaseOrder.expected_delivery_date') }}</h4>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $purchaseOrder->expected_delivery_date?->translatedFormat('M d, Y') ?? '-' }}
                        </p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">{{ trans('inventory::modules.purchaseOrder.items') }}</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ trans('inventory::modules.inventoryItem.name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ trans('inventory::modules.purchaseOrder.unit_price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ trans('inventory::modules.purchaseOrder.ordered_quantity') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ trans('inventory::modules.purchaseOrder.received_quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($purchaseOrder->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $item->inventoryItem->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ currency_format($item->unit_price, restaurant()->currency_id) }}

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ number_format($item->quantity, 2) }}
                                            <span class="text-gray-500 dark:text-gray-400">
                                                ({{ $item->inventoryItem->unit->symbol }})
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ number_format($item->received_quantity, 2) }}
                                            <span class="text-gray-500 dark:text-gray-400">
                                                ({{ $item->inventoryItem->unit->symbol }})
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-lg text-gray-900 dark:text-white font-bold text-right">
                                        {{ trans('modules.billing.total') }}
                                    </td>
                                    <td colspan="1" class="px-6 py-4 whitespace-nowrap text-lg text-gray-900 dark:text-white font-bold">
                                        {{ currency_format($purchaseOrder->total_amount, restaurant()->currency_id) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                @if($purchaseOrder->notes)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ trans('inventory::modules.purchaseOrder.notes') }}</h4>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $purchaseOrder->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end">
                <div class="flex space-x-3">
                    <x-button wire:click="downloadPdf" wire:loading.attr="disabled" class="mr-3 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ trans('inventory::modules.purchaseOrder.download_pdf') }}
                    </x-button>

                    <x-secondary-button wire:click="$set('showModal', false)">
                        {{ trans('app.close') }}
                    </x-secondary-button>
                </div>
            </div>
        </div>
        @endif
    </x-modal>
</div> 