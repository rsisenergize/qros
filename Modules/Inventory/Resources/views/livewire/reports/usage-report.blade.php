@php
use Modules\Inventory\Entities\InventoryMovement;
@endphp

<div>
   
    <x-inventory::reports.tabs />

    <!-- Title Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('inventory::modules.reports.usage.title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('inventory::modules.reports.usage.description') }}
        </p>
    </div>

    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Period Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.period') }}
                    </label>
                    <select wire:model.live="period" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="daily">{{ __('inventory::modules.reports.filters.periods.daily') }}</option>
                        <option value="weekly">{{ __('inventory::modules.reports.filters.periods.weekly') }}</option>
                        <option value="monthly">{{ __('inventory::modules.reports.filters.periods.monthly') }}</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.start_date') }}
                    </label>
                    <input type="date" wire:model.live="startDate" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.end_date') }}
                    </label>
                    <input type="date" wire:model.live="endDate" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.search_items') }}
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="{{ __('inventory::modules.reports.filters.search_placeholder') }}">
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                    {{ __('inventory::modules.reports.usage.actual_usage') }}
                </h3>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ number_format($totalUsage, 2, '.', ',') }}
                </p>
                @php
                    $percentageChange = $previousPeriodUsage != 0 
                        ? (($totalUsage - $previousPeriodUsage) / $previousPeriodUsage) * 100 
                        : 0;
                @endphp
                <div class="mt-2 flex items-center text-sm {{ $percentageChange >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        @if($percentageChange >= 0)
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"/>
                        @else
                            <path fill-rule="evenodd" d="M12 13a1 1 0 110 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"/>
                        @endif
                    </svg>
                    <span class="ml-2">{{ number_format(abs($percentageChange), 1) }}% {{ $percentageChange >= 0 ? __('inventory::modules.reports.usage.increase') : __('inventory::modules.reports.usage.decrease') }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                    {{ __('inventory::modules.reports.usage.expected_usage') }}
                </h3>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ number_format($expectedUsage->sum(), 2, '.', ',') }}
                </p>
                @php
                    $expectedPercentageChange = $previousExpectedUsage->sum() != 0 
                        ? (($expectedUsage->sum() - $previousExpectedUsage->sum()) / $previousExpectedUsage->sum()) * 100 
                        : 0;
                @endphp
                <div class="mt-2 flex items-center text-sm {{ $expectedPercentageChange >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        @if($expectedPercentageChange >= 0)
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"/>
                        @else
                            <path fill-rule="evenodd" d="M12 13a1 1 0 110 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"/>
                        @endif
                    </svg>
                    <span class="ml-2">{{ number_format(abs($expectedPercentageChange), 1) }}% {{ $expectedPercentageChange >= 0 ? __('inventory::modules.reports.usage.increase') : __('inventory::modules.reports.usage.decrease') }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                    {{ __('inventory::modules.reports.usage.variance') }}
                </h3>
                @php
                    $variance = $expectedUsage->sum() - $totalUsage;
                    $variancePercentage = $expectedUsage->sum() != 0 ? ($variance / $expectedUsage->sum()) * 100 : 0;
                @endphp
                <p class="mt-2 text-3xl font-bold {{ $variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ number_format($variance, 2, '.', ',') }}
                </p>
                <div class="mt-2 flex items-center text-sm {{ $variance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        @if($variance >= 0)
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"/>
                        @else
                            <path fill-rule="evenodd" d="M12 13a1 1 0 110 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"/>
                        @endif
                    </svg>
                    <span class="ml-2">{{ number_format(abs($variancePercentage), 1) }}% {{ $variance >= 0 ? __('inventory::modules.reports.usage.under_usage') : __('inventory::modules.reports.usage.over_usage') }}</span>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('inventory::modules.reports.usage.trends_title') }}
            </h3>
            <div class="mt-4">
                <div id="usageChart"></div>
            </div>
        </div>

        <!-- Expected Usage Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('inventory::modules.reports.usage.expected_usage_breakdown') }}
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('inventory::modules.reports.usage.expected_usage_description') }}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.item') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.expected_quantity') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.actual_quantity') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.variance') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        @foreach($expectedUsage as $inventoryItemId => $expectedQuantity)
                            @php
                                $inventoryItem = \Modules\Inventory\Entities\InventoryItem::find($inventoryItemId);
                                $actualQuantity = $movements->filter(function($movement) use ($inventoryItemId) {
                                    return $movement->item && $movement->item->id == $inventoryItemId;
                                })->sum('quantity');
                                $variance = $expectedQuantity - $actualQuantity;
                            @endphp
                            @if($inventoryItem)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ $inventoryItem->name }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="text-gray-900 dark:text-gray-100">{{ number_format($expectedQuantity, 2, '.', ',') }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $inventoryItem->unit->symbol ?? $inventoryItem->unit->name }}</span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="text-gray-900 dark:text-gray-100">{{ number_format($actualQuantity, 2, '.', ',') }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $inventoryItem->unit->symbol ?? $inventoryItem->unit->name }}</span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full {{ $variance >= 0 ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                        {{ number_format($variance, 2, '.', ',') }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actual Usage Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('inventory::modules.reports.usage.actual_usage_details') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.item') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.quantity') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.date') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.usage.transaction_type') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        @foreach($movements as $movement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $movement->item->name }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="text-gray-900 dark:text-gray-100">{{ number_format($movement->quantity, 2, '.', ',') }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $movement->item->unit->symbol ?? $movement->item->unit->name }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $movement->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @switch($movement->transaction_type)
                                    @case($transactionTypes['STOCK_ADDED'])
                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            {{ __('inventory::modules.stock.stockIn') }}
                                        </span>
                                        @break
                                    @case($transactionTypes['ORDER_USED'])
                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            {{ __('inventory::modules.stock.stockOut') }}
                                        </span>
                                        @break
                                    @case($transactionTypes['WASTE'])
                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100">
                                            {{ __('inventory::modules.stock.waste') }}
                                        </span>
                                        @break
                                    @case($transactionTypes['TRANSFER'])
                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            {{ __('inventory::modules.stock.transfer') }}
                                        </span>
                                        @break
                                    @default
                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                            {{ ucfirst($movement->transaction_type) }}
                                        </span>
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-2 dark:bg-gray-800">
                {{ $movements->links() }}
            </div>
        </div>
    </div>

    @script
    <script>
        let usageChart;
        
        $wire.on('updateChart', ({ options }) => {
            if (usageChart) {
                usageChart.destroy();
            }
            
            usageChart = new ApexCharts(document.getElementById('usageChart'), options);
            usageChart.render();
        });
    </script>
    @endscript
</div> 