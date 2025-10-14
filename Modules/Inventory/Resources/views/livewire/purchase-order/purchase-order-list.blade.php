<div class="p-4">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/50">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ trans('inventory::modules.purchaseOrder.total_orders') }}</h3>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/50">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ trans('inventory::modules.purchaseOrder.pending_orders') }}</h3>
                    <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_orders'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/50">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ trans('inventory::modules.purchaseOrder.completed_orders') }}</h3>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $stats['completed_orders'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <x-input type="text" wire:model.live.debounce.300ms="search" 
                       class="block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                       placeholder="{{ trans('inventory::modules.purchaseOrder.search_placeholder') }}" />
            </div>
            <div>
                <x-select wire:model.live="supplierId" 
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">{{ trans('inventory::modules.purchaseOrder.all_suppliers') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select wire:model.live="status" 
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">{{ trans('inventory::modules.purchaseOrder.all_status') }}</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-secondary-button wire:click="clearFilters">
                    {{ trans('inventory::modules.purchaseOrder.clear_filters') }}
                </x-secondary-button>
            </div>

        </div>
       
    </div>

    @if(user_can('Create Purchase Order'))
    <div class="mb-6 flex justify-end">
        <x-button wire:click="$dispatch('showPurchaseOrderModal')">
            {{ trans('inventory::modules.purchaseOrder.create_title') }}
        </x-button>
    </div>
    @endif

    <!-- Purchase Orders Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ trans('inventory::modules.purchaseOrder.po_number') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ trans('inventory::modules.purchaseOrder.supplier') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ trans('inventory::modules.purchaseOrder.order_date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ trans('inventory::modules.purchaseOrder.expected_delivery_date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ trans('app.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ trans('inventory::modules.purchaseOrder.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($purchaseOrders as $purchaseOrder)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $purchaseOrder->po_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <a href="{{ route('suppliers.show', $purchaseOrder->supplier->id) }}" class="underline underline-offset-1" wire:navigate>
                                    {{ $purchaseOrder->supplier->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $purchaseOrder->order_date->translatedFormat('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $purchaseOrder->expected_delivery_date?->translatedFormat('M d, Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $purchaseOrder->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                    {{ $purchaseOrder->status === 'sent' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300' : '' }}
                                    {{ $purchaseOrder->status === 'received' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : '' }}
                                    {{ $purchaseOrder->status === 'partially_received' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300' : '' }}
                                    {{ $purchaseOrder->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300' : '' }}">
                                    {{ $statuses[$purchaseOrder->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="static" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            @click.away="open = false"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full focus:outline-none relative">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open"
                                         x-transition
                                         class="fixed right-0 z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                                         x-cloak
                                         @click.away="open = false"
                                         x-data="{ style: {} }"
                                         x-init="$nextTick(() => {
                                             const button = $el.previousElementSibling;
                                             const rect = button.getBoundingClientRect();
                                             style = {
                                                 top: `${rect.bottom + window.scrollY + 5}px`,
                                                 right: `${window.innerWidth - rect.right}px`
                                             }
                                         })"
                                         :style="style">
                                        <div class="py-1 flex flex-col gap-1">
                                            @if($purchaseOrder->status === 'draft' && user_can('Update Purchase Order'))
                                                <button wire:click="confirmSend({{ $purchaseOrder->id }})" @click="open = false"
                                                        class="w-full flex items-center px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/50">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                    </svg>
                                                    <span>{{ trans('inventory::modules.purchaseOrder.send') }}</span>
                                                </button>
                                            @endif
                                            
                                            @if(!in_array($purchaseOrder->status, ['received', 'cancelled']) && user_can('Update Purchase Order'))
                                                <button wire:click="$dispatch('editPurchaseOrder', { purchaseOrder: {{ $purchaseOrder->id }} })" @click="open = false"
                                                        class="w-full flex items-center px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/50">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span>{{ trans('inventory::modules.purchaseOrder.edit') }}</span>
                                                </button>
                                            @endif
                                            
                                            @if(in_array($purchaseOrder->status, ['sent', 'partially_received']) && user_can('Update Purchase Order'))
                                                <button wire:click="$dispatch('showReceiveModal', { purchaseOrder: {{ $purchaseOrder->id }} })"
                                                        class="inline-flex items-center px-4 py-2 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/50 rounded-lg">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                    </svg>
                                                    <span>{{ trans('inventory::modules.purchaseOrder.receive') }}</span>
                                                </button>
                                            @endif

                                            @if(user_can('Show Purchase Order'))
                                                <button wire:click="$dispatch('viewPurchaseOrder', { purchaseOrder: {{ $purchaseOrder->id }} })"
                                                        class="inline-flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900/50 rounded-lg">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>{{ trans('inventory::modules.purchaseOrder.view') }}</span>
                                                </button>
                                            @endif

                                            @if(user_can('Show Purchase Order'))
                                            <button wire:click="downloadPdf({{ $purchaseOrder->id }})"
                                                    class="inline-flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900/50 rounded-lg">
                                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                    <span>{{ trans('inventory::modules.purchaseOrder.download_pdf') }}</span>
                                                </button>
                                            @endif

                                            @if(in_array($purchaseOrder->status, ['draft', 'sent']) && user_can('Update Purchase Order'))
                                                <button wire:click="confirmCancel({{ $purchaseOrder->id }})"
                                                        class="inline-flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900/50 rounded-lg">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    <span>{{ trans('app.cancel') }}</span>
                                                </button>
                                            @endif


                                            @if(!in_array($purchaseOrder->status, ['received', 'cancelled']) && user_can('Delete Purchase Order'))
                                                <button wire:click="confirmDelete({{ $purchaseOrder->id }})"
                                                        class="inline-flex items-center px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-lg">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span>{{ trans('inventory::modules.purchaseOrder.delete') }}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                {{ trans('inventory::modules.purchaseOrder.no_records') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $purchaseOrders->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingDeletion">
        <x-slot name="title">
            {{ trans('inventory::modules.purchaseOrder.delete_title') }}
        </x-slot>

        <x-slot name="content">
            {{ trans('inventory::modules.purchaseOrder.delete_confirm') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingDeletion', false)" wire:loading.attr="disabled">
                {{ trans('inventory::modules.purchaseOrder.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="delete" wire:loading.attr="disabled">
                {{ trans('inventory::modules.purchaseOrder.delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Send Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingSend">
        <x-slot name="title">
            {{ trans('inventory::modules.purchaseOrder.send_title') }}
        </x-slot>

        <x-slot name="content">
            {{ trans('inventory::modules.purchaseOrder.send_confirm') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingSend', false)" wire:loading.attr="disabled">
                {{ trans('app.cancel') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="send" wire:loading.attr="disabled">
                {{ trans('inventory::modules.purchaseOrder.send') }}
            </x-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Cancel Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingCancel">
        <x-slot name="title">
            {{ trans('inventory::modules.purchaseOrder.cancel_title') }}
        </x-slot>

        <x-slot name="content">
            {{ trans('inventory::modules.purchaseOrder.cancel_confirm') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingCancel', false)" wire:loading.attr="disabled">
                {{ trans('app.cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="cancel" wire:loading.attr="disabled">
                {{ trans('inventory::modules.purchaseOrder.cancel') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <livewire:inventory::purchase-order.manage-purchase-order />
    <livewire:inventory::purchase-order.receive-purchase-order />
    <livewire:inventory::purchase-order.view-purchase-order />
</div> 