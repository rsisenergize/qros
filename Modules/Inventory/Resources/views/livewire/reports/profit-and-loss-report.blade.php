<div class="space-y-6">

    <x-inventory::reports.tabs />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('inventory::modules.reports.profit_and_loss_report') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('inventory::modules.reports.profit_loss_description') }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                {{ __('inventory::modules.reports.modifier_options_note') }}
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Start Date -->
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('inventory::modules.reports.start_date') }}
                </label>
                <input type="datetime-local" 
                       wire:model.live="startDate" 
                       id="startDate"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- End Date -->
            <div>
                <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('inventory::modules.reports.end_date') }}
                </label>
                <input type="datetime-local" 
                       wire:model.live="endDate" 
                       id="endDate"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="selectedCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('inventory::modules.reports.category') }}
                </label>
                <select wire:model.live="selectedCategory" 
                        id="selectedCategory"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="all">{{ __('inventory::modules.reports.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->getTranslation('category_name', app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Generate Report Button -->
            <div class="flex items-end">
                <button wire:click="generateReport" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    {{ __('inventory::modules.reports.generate_report') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Sales -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('inventory::modules.reports.total_sales') }}
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ currency_format($totalSales, restaurant()->currency_id) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('inventory::modules.reports.total_cost') }}
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ currency_format($totalCost, restaurant()->currency_id) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Profit -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $totalProfit >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $totalProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('inventory::modules.reports.total_profit') }}
                    </p>
                    <p class="text-2xl font-semibold {{ $totalProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ currency_format($totalProfit, restaurant()->currency_id) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('inventory::modules.reports.menu_item_breakdown') }}
                </h3>
                @if(count($reportData) > 0)
                    <div class="mt-3 sm:mt-0 flex space-x-2">
                        <button wire:click="exportCsv" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded-md transition duration-200">
                            {{ __('inventory::modules.reports.export_csv') }}
                        </button>
                        
                    </div>
                @endif
            </div>
        </div>

        @if(count($reportData) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.menu_item') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.category') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.quantity_sold') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.avg_price') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.total_sales') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.ingredient_cost') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.profit') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('inventory::modules.reports.profit_margin') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($reportData as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item['item_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item['category_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $item['quantity_sold'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ currency_format($item['avg_unit_price'], restaurant()->currency_id) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400 font-medium">
                                    {{ currency_format($item['total_sales'], restaurant()->currency_id) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400 font-medium">
                                    {{ currency_format($item['ingredient_cost'], restaurant()->currency_id) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $item['profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ currency_format($item['profit'], restaurant()->currency_id) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ $item['profit_margin'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format($item['profit_margin'], 1) }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('inventory::modules.reports.no_data_found') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('inventory::modules.reports.no_data_description') }}
                </p>
            </div>
        @endif
    </div>
</div>
