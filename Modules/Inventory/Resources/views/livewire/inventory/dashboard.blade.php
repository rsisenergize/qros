<div class="p-6 bg-gray-50 dark:bg-gray-900">
    <!-- Header with Filters -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">{{ __('inventory::modules.dashboard.title') }}</h2>
        <div class="flex flex-wrap gap-4 items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('inventory::modules.dashboard.filters.category') }}</label>
                <select wire:model.live="selectedCategory" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">{{ __('inventory::modules.dashboard.filters.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('inventory::modules.dashboard.filters.time_period') }}</label>
                <select wire:model.live="selectedPeriod" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(['daily', 'weekly', 'monthly'] as $period)
                        <option value="{{ $period }}">{{ __("inventory::modules.dashboard.filters.periods.$period") }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Stock Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($stockLevels as $stock)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stock['category'] }}</h4>
                        <span @class([
                            'px-2 py-1 text-xs rounded-full',
                            'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' => $stock['status'] === 'adequate',
                            'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' => $stock['status'] === 'low-stock',
                            'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' => $stock['status'] === 'out-of-stock'
                        ])>
                            {{ __("inventory::modules.dashboard.stock.status.{$stock['status']}") }}
                        </span>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stock['stock'] }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('inventory::modules.dashboard.stock.items') }}</span>
                    </div>
                    @if($stock['low_stock_count'] > 0)
                        <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                            {{ trans_choice('inventory::modules.dashboard.stock.below_threshold', $stock['low_stock_count'], ['count' => $stock['low_stock_count']]) }}
                        </p>
                    @endif
                    @if($stock['out_of_stock_count'] > 0)
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ trans_choice('inventory::modules.dashboard.stock.out_of_stock', $stock['out_of_stock_count'], ['count' => $stock['out_of_stock_count']]) }}
                        </p>
                    @endif
                </div>
                <div @class([
                    'h-1 w-full',
                    'bg-green-500' => $stock['status'] === 'adequate',
                    'bg-yellow-500' => $stock['status'] === 'low-stock',
                    'bg-red-500' => $stock['status'] === 'out-of-stock'
                ])></div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Moving Items -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('inventory::modules.dashboard.sections.top_moving.title') }}</h3>
            <div class="space-y-3">
                @foreach($topMovingItems as $item)
                    <div @class([
                        'flex flex-col p-3 rounded-lg transition-colors',
                        'bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600'
                    ])>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['name'] }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $item['category'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                {{ __('inventory::modules.dashboard.sections.top_moving.stock') }}: {{ $item['current_stock'] }} {{ $item['unit'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-green-600 dark:text-green-400">
                                {{ __('inventory::modules.dashboard.sections.top_moving.usage') }}: {{ $item['usage'] }} {{ $item['unit'] }}
                            </span>
                            @if($item['waste'] > 0)
                                <span class="text-red-600 dark:text-red-400">
                                    {{ __('inventory::modules.dashboard.sections.top_moving.waste') }}: {{ $item['waste'] }} {{ $item['unit'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('inventory::modules.dashboard.sections.low_stock.title') }}</h3>
                @if(count($lowStockItems) > 0)
                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                        {{ trans_choice('inventory::modules.dashboard.sections.low_stock.alerts', count($lowStockItems), ['count' => count($lowStockItems)]) }}
                    </span>
                @endif
            </div>
            <div class="space-y-3">
                @forelse($lowStockItems as $item)
                    <div @class([
                        'p-3 rounded-lg border-l-4',
                        'border-red-500 bg-red-50 dark:bg-red-900/20' => $item['current_stock'] <= 0,
                        'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' => $item['current_stock'] > 0
                    ])>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['name'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $item['category'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span @class([
                                'flex items-center',
                                'text-red-700 dark:text-red-300' => $item['current_stock'] <= 0,
                                'text-yellow-700 dark:text-yellow-300' => $item['current_stock'] > 0
                            ])>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                {{ __('inventory::modules.dashboard.sections.low_stock.current') }}: {{ $item['current_stock'] }} {{ $item['unit'] }}
                            </span>
                            <span @class([
                                'font-medium',
                                'text-red-700 dark:text-red-300' => $item['current_stock'] <= 0,
                                'text-yellow-700 dark:text-yellow-300' => $item['current_stock'] > 0
                            ])>
                                {{ __('inventory::modules.dashboard.sections.low_stock.threshold') }}: {{ $item['threshold'] }} {{ $item['unit'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="block">{{ __('inventory::modules.dashboard.sections.low_stock.no_items') }}</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Usage-Stock Correlation -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('inventory::modules.dashboard.sections.correlation.title') }}</h3>
        <div class="space-y-4">
            @foreach($salesStockCorrelation as $item)
                <div class="border dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['name'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">{{ $item['category'] }}</span>
                        </div>
                        <span @class([
                            'px-2 py-1 text-xs rounded-full',
                            $item['status']['class'],
                            str_replace(['bg-', 'text-'], ['dark:bg-', 'dark:text-'], $item['status']['class'])
                        ])>
                            {{ $item['status']['status'] }}
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-2">
                        <div class="text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('inventory::modules.dashboard.sections.correlation.current_stock') }}</span>
                            <span class="block font-medium text-gray-900 dark:text-gray-100">{{ $item['current_stock'] }} {{ $item['unit'] }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('inventory::modules.dashboard.sections.correlation.usage') }}</span>
                            <span class="block font-medium text-red-600 dark:text-red-400">{{ $item['usage'] }} {{ $item['unit'] }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('inventory::modules.dashboard.sections.correlation.stock_added') }}</span>
                            <span class="block font-medium text-green-600 dark:text-green-400">{{ $item['stock_added'] }} {{ $item['unit'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Expiring Stock -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mt-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('inventory::modules.dashboard.sections.expiring_stock.title') }}</h3>
            <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 rounded-full">
                {{ count($expiringStockItems) }} {{ __('inventory::modules.dashboard.sections.expiring_stock.items') }}
            </span>
        </div>
        <div class="space-y-3">
            @foreach($expiringStockItems as $item)
                <div class="p-3 rounded-lg border-l-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item->item->name }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $item->item->category->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center text-orange-700 dark:text-orange-300">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('inventory::modules.dashboard.sections.expiring_stock.expires_in', ['days' => intval($item->expiration_date->diffInDays(now(), true))]) }}
                    </span>
                    <span class="font-medium text-orange-700 dark:text-orange-300">
                        {{ __('inventory::modules.dashboard.sections.expiring_stock.stock') }}: {{ $item->quantity }} {{ $item->item->unit->symbol }}
                    </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
