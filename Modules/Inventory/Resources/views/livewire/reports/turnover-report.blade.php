<div>
    
    <x-inventory::reports.tabs />
    <!-- Title Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('inventory::modules.reports.turnover.title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('inventory::modules.reports.turnover.description') }}
        </p>
    </div>

    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.start_date') }}
                    </label>
                    <input type="date" 
                        wire:model.live="startDate" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.end_date') }}
                    </label>
                    <input type="date" 
                        wire:model.live="endDate" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('inventory::modules.reports.filters.search_items') }}
                    </label>
                    <input type="text" 
                        wire:model.live.debounce.300ms="searchTerm" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="{{ __('inventory::modules.reports.filters.search_placeholder') }}">
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('inventory::modules.reports.turnover.top_items') }}
            </h3>
            <div class="mt-4">
                <div id="turnoverChart"></div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.inventoryItem.name') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.turnover.current_stock') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.turnover.usage_count') }}
                            </th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.turnover.turnover_rate') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                {{ $item->name }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="text-gray-900 dark:text-gray-100">{{ number_format($item->stocks_sum_quantity, 2, '.', ',') }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $item->unit->symbol ?? $item->unit->name }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                <div>
                                    <span class="font-medium">{{ number_format($item->usage_count, 2, '.', ',') }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $item->unit->symbol ?? $item->unit->name }}</span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ trans_choice('inventory::modules.reports.turnover.transaction_count', $item->movement_count, ['count' => $item->movement_count]) }}
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full 
                                    {{ $item->turnover_rate <= 1 
                                        ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' 
                                        : ($item->turnover_rate <= 2 
                                            ? 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100' 
                                            : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100') }}">
                                    {{ number_format($item->turnover_rate, 2, '.', ',') }}x
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
        let turnoverChart;
        
        if (document.getElementById('turnoverChart')) {
            turnoverChart = new ApexCharts(document.getElementById('turnoverChart'), getChartOptions());
            turnoverChart.render();
        }

        function getChartOptions() {
            let mainChartColors = {}

            if (document.documentElement.classList.contains('dark')) {
                mainChartColors = {
                    borderColor: '#374151',
                    labelColor: '#9CA3AF',
                    opacityFrom: 0,
                    opacityTo: 0.15,
                };
            } else {
                mainChartColors = {
                    borderColor: '#F3F4F6',
                    labelColor: '#6B7280',
                    opacityFrom: 0.45,
                    opacityTo: 0,
                }
            }

            return $wire.chartOptions;
        }

        $wire.on('updateTurnoverChart', ({ options }) => {
            if (turnoverChart) {
                turnoverChart.destroy();
            }
            
            turnoverChart = new ApexCharts(document.getElementById('turnoverChart'), options);
            turnoverChart.render();
        });
    </script>
    @endscript
</div> 