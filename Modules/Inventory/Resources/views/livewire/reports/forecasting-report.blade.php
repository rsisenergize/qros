<div>

    <x-inventory::reports.tabs />

    <!-- Title Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('inventory::modules.reports.forecasting.title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('inventory::modules.reports.forecasting.description') }}
        </p>
    </div>

    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.search_items') }}
                    </label>
                    <input type="text" 
                        wire:model.live="searchTerm" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="{{ __('inventory::modules.reports.filters.search_placeholder') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.select_item') }}
                    </label>
                    <select wire:model.live="selectedItem" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('inventory::modules.reports.filters.all_items') }}</option>
                        @foreach($itemsList as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.forecast_period') }}
                    </label>
                    <select wire:model.live="period" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="7">{{ __('inventory::modules.reports.filters.periods.week') }}</option>
                        <option value="15">{{ __('inventory::modules.reports.filters.periods.fortnight') }}</option>
                        <option value="30">{{ __('inventory::modules.reports.filters.periods.month') }}</option>
                        <option value="60">{{ __('inventory::modules.reports.filters.periods.two_months') }}</option>
                        <option value="90">{{ __('inventory::modules.reports.filters.periods.quarter') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('inventory::modules.reports.forecasting.title') }}
            </h3>
            <div class="mt-4">
                <div id="forecastChart"></div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.forecasting.item') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.forecasting.current_stock') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.forecasting.usage_count') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.forecasting.avg_daily_usage') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.forecasting.estimated_days') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $item->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="text-gray-900 dark:text-gray-100">{{ number_format($item->current_stock, 2, '.', ',') }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $item->unit->symbol ?? $item->unit->name }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                <div>
                                    <span class="font-medium">{{ number_format($item->usage_count, 2, '.', ',') }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $item->unit->symbol ?? $item->unit->name }}</span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ trans_choice('inventory::modules.reports.forecasting.transaction_count', $item->movement_count, ['count' => $item->movement_count]) }}
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="text-gray-900 dark:text-gray-100">{{ number_format($item->daily_usage, 2, '.', ',') }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $item->unit->symbol ?? $item->unit->name }} {{ __('inventory::modules.reports.forecasting.per_day') }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full 
                                    {{ $item->days_left <= 7 ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 
                                       ($item->days_left <= 15 ? 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100' : 
                                       'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100') }}">
                                    {{ __('inventory::modules.reports.forecasting.days_left', ['days' => $item->days_left]) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-2 dark:bg-gray-800">
                {{ $items->links() }}
            </div>
        </div>
    </div>

    @script
    <script>
        let forecastChart;
        
        $wire.on('updateForecastChart', ({ options }) => {
            if (forecastChart) {
                forecastChart.destroy();
            }
            
            forecastChart = new ApexCharts(document.getElementById('forecastChart'), options);
            forecastChart.render();
        });
    </script>
    @endscript
</div> 