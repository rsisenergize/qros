<div class="py-6 px-4 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">@lang("inventory::modules.stock.stockInventory")</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang("inventory::modules.stock.stockInventoryDescription")</p>
        </div>

        @if(user_can('Create Inventory Movement'))
        <x-button wire:click="$set('showAddStockEntry', true)" >
            @lang("inventory::modules.stock.addStockEntry")
        </x-button>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Available Items -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-lg shadow-sm">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/50 p-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang("inventory::modules.stock.availableItems")</p>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ number_format($stats['available_items']) }}</h3>
                        </div>
                    </div>
                    <div class="text-green-600 dark:text-green-400">
                        <span class="text-sm font-medium">+{{ number_format($stats['available_items'] / max(array_sum($stats), 1) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-800/30 rounded-lg shadow-sm">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/50 p-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang("inventory::modules.stock.lowStockItems")</p>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ number_format($stats['low_stock']) }}</h3>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 rounded-lg shadow-sm">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/50 p-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </span>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang("inventory::modules.stock.outOfStock")</p>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ number_format($stats['out_of_stock']) }}</h3>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-lg shadow-sm">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/50 p-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">@lang("inventory::modules.stock.totalCost")</p>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ currency_format($stats['total_cost'], restaurant()->currency_id) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="@lang('inventory::modules.stock.searchPlaceholder')"
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                <div class="absolute left-3 top-2.5">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <select wire:model.live="category" class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-4 focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                <option value="">@lang('inventory::modules.stock.allCategories')</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="stockStatus" class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white py-2 px-4 focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                <option value="">@lang('inventory::modules.stock.allStatus')</option>
                <option value="in_stock">@lang('inventory::modules.stock.inStock')</option>
                <option value="low_stock">@lang('inventory::modules.stock.lowStock')</option>
                <option value="out_of_stock">@lang('inventory::modules.stock.outOfStock')</option>
            </select>

            @if($search || $category || $stockStatus)
                <button
                    wire:click="clearFilters"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    @lang('inventory::modules.stock.clearFilters')
                </button>
            @endif
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang("inventory::modules.inventoryItem.name")</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang("inventory::modules.inventoryItem.category")</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang("inventory::modules.stock.currentStock")</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang("inventory::modules.stock.stockStatus")</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">@lang("inventory::modules.stock.cost")</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($stockItems as $item)
                        @php
                            $stockStatus = $item->getStockStatus();
                            $nearestExpiry = $item->stocks->min('expiry_date');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">#{{ $item->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $item->category->name ?? '-'}}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ number_format($item->current_stock, 2) }} {{ $item->unit->symbol }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">@lang("inventory::modules.stock.minStock"): {{ number_format($item->threshold_quantity, 2) }} {{ $item->unit->symbol }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockStatus['class'] }}">
                                    {{ $stockStatus['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ currency_format($item->unit_purchase_price * $item->current_stock, restaurant()->currency_id) }}</div>
                            </td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                @lang("inventory::modules.stock.noStockItemsFound")
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $stockItems->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showAddStockEntry">
        <x-slot name="title">
            @lang("inventory::modules.stock.addStockEntry")
        </x-slot>

        <x-slot name="content">
            <livewire:inventory::stock.add-stock-entry />
        </x-slot>
    </x-right-modal>
</div>
