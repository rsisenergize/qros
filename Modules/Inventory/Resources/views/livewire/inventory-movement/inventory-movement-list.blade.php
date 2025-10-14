<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 md:p-6">
    <!-- Header Section with Stats -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ __('inventory::modules.movements.title') }}</h2>
            <div class="flex gap-2">
                <x-button
                    wire:click="$toggle('showAddStockEntry')"
                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    {{ __('inventory::modules.stock.addStockEntry') }}
                </x-button>

            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-900">
                <div class="text-green-600 dark:text-green-400 text-sm font-medium">{{ __('inventory::modules.movements.stock_in.title') }}</div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ number_format($totalStockIn, 2) }}</div>
                <div class="text-green-600 dark:text-green-400 text-sm">{{ __('inventory::modules.movements.stock_in.subtitle') }}</div>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-100 dark:border-red-900">
                <div class="text-red-600 dark:text-red-400 text-sm font-medium">{{ __('inventory::modules.movements.stock_out.title') }}</div>
                <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ number_format($totalStockOut, 2) }}</div>
                <div class="text-red-600 dark:text-red-400 text-sm">{{ __('inventory::modules.movements.stock_out.subtitle') }}</div>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-100 dark:border-yellow-900">
                <div class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">{{ __('inventory::modules.movements.waste.title') }}</div>
                <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ number_format($totalWaste ?? 0, 2) }}</div>
                <div class="text-yellow-600 dark:text-yellow-400 text-sm">{{ __('inventory::modules.movements.waste.subtitle') }}</div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-900">
                <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">{{ __('inventory::modules.movements.transfers.title') }}</div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ number_format($totalTransfers, 2) }}</div>
                <div class="text-blue-600 dark:text-blue-400 text-sm">{{ __('inventory::modules.movements.transfers.subtitle') }}</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-6">
            <!-- Search Box -->
            <div class="relative flex-1">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="{{ __('inventory::modules.movements.filters.search_placeholder') }}">
                <div class="absolute left-3 top-2.5 text-gray-400 dark:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <select wire:model.live="filterType" class="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('inventory::modules.movements.filters.all_types') }}</option>
                    @foreach(['in', 'out', 'waste', 'transfer'] as $type)
                        <option value="{{ $type }}">{{ __('inventory::modules.movements.filters.types.' . $type) }}</option>
                    @endforeach
                </select>

                <select wire:model.live="category" class="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('inventory::modules.movements.filters.all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="dateRange" class="border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    <option value="today">{{ __('inventory::modules.movements.filters.date_ranges.today') }}</option>
                    <option value="week">{{ __('inventory::modules.movements.filters.date_ranges.week') }}</option>
                    <option value="month">{{ __('inventory::modules.movements.filters.date_ranges.month') }}</option>
                    <option value="quarter">{{ __('inventory::modules.movements.filters.date_ranges.quarter') }}</option>
                </select>

                @if($search || $filterType || $category || $dateRange !== 'month')
                    <button
                        wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ __('inventory::modules.movements.filters.clear_filters') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="relative">
        <!-- Info Message -->
        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    {{ __('inventory::modules.movements.edit_restriction_message') }}
                </p>
            </div>
        </div>



        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th wire:click="sortBy('created_at')" class="group px-6 py-3 text-left cursor-pointer">
                            <div class="flex items-center space-x-1">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('inventory::modules.movements.table.date_time') }}</span>
                                @if($sortField === 'created_at')
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('inventory::modules.movements.table.item_category') }}</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('inventory::modules.movements.table.movement') }}</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('inventory::modules.movements.table.quantity_unit') }}</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('inventory::modules.movements.table.supplier') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('inventory::modules.movements.table.staff') }}
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('inventory::modules.movements.table.actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $movement->created_at->timezone(timezone())->translatedFormat('M d, Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->created_at->timezone(timezone())->translatedFormat('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $movement->item->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->item->category ? $movement->item->category->name : '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span @class([
                                    'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' => $movement->transaction_type === 'in',
                                    'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' => $movement->transaction_type === 'out',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' => $movement->transaction_type === 'waste',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' => $movement->transaction_type === 'transfer',
                                ])>
                                    {{ ucfirst($movement->transaction_type) }}
                                    @if($movement->waste_reason)
                                        - {{ ucfirst($movement->waste_reason) }}
                                    @endif


                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $movement->quantity }} {{ $movement->item->unit->symbol }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $movement->supplier->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $movement->addedBy->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center space-x-3 relative">
                                    <button
                                        wire:click="viewDetails({{ $movement->id }})"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    @if($movement->created_at->diffInDays(now()) < 7)
                                        <button
                                            wire:click="edit({{ $movement->id }})"
                                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    @else
                                        <div x-data="{ tooltip: false }">
                                            <button
                                                @mouseenter="tooltip = true"
                                                @mouseleave="tooltip = false"
                                                class="text-gray-400 dark:text-gray-600 cursor-not-allowed p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <div
                                                x-show="tooltip"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed ml-2 -translate-x-full translate-y-[-50%] w-48 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg z-50 whitespace-normal"
                                                style="pointer-events: none; top: 50%;"
                                            >
                                            <div class="font-medium">{!! __('inventory::modules.movements.edit_restriction_tooltip') !!}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <span class="text-gray-500 dark:text-gray-400 text-lg">{{ __('inventory::modules.movements.no_movements') }}</span>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">{{ __('inventory::modules.movements.try_adjusting') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $movements->links() }}
    </div>


    <x-right-modal wire:model.live="showAddStockEntry">
        <x-slot name="title">
            @lang("inventory::modules.stock.addStockEntry")
        </x-slot>

        <x-slot name="content">
            <livewire:inventory::stock.add-stock-entry />
        </x-slot>
    </x-right-modal>

    <!-- View Modal -->
    <x-right-modal wire:model.live="showViewModal">
        <x-slot name="title">
            @lang("inventory::modules.movements.viewMovement")
        </x-slot>

        <x-slot name="content">
            @if($selectedMovement)
                <livewire:inventory::inventory-movement.view-movement :movement="$selectedMovement" :key="'view-'.$selectedMovement->id" />
            @endif
        </x-slot>
    </x-right-modal>

    <!-- Edit Modal -->
    <x-right-modal wire:model.live="showEditModal">
        <x-slot name="title">
            @lang("inventory::modules.movements.editMovement")
        </x-slot>

        <x-slot name="content">
            @if($selectedMovement)
                <livewire:inventory::inventory-movement.edit-movement :movement="$selectedMovement" :key="'edit-'.$selectedMovement->id" />
            @endif
        </x-slot>
    </x-right-modal>
</div>
