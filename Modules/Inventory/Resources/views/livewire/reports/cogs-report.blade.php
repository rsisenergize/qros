<div>

    <x-inventory::reports.tabs />

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">{{ __('inventory::modules.reports.cogs.title') }}</h2>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('inventory::modules.reports.cogs.filters.start_date') }}</label>
                    <input type="date" wire:model="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('inventory::modules.reports.cogs.filters.end_date') }}</label>
                    <input type="date" wire:model="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('inventory::modules.reports.cogs.filters.category') }}</label>
                    <select wire:model="selectedCategory" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">{{ __('inventory::modules.reports.cogs.filters.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <x-button wire:click="generateReport">
                    {{ __('inventory::modules.reports.cogs.filters.generate_report') }}
                </x-button>
            </div>

      
        </div>
    </div>

    <!-- Summary Card -->
    <div class="mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-sm font-medium text-blue-800">{{ __('inventory::modules.reports.cogs.summary.total_cogs') }}</h3>
            <p class="text-2xl font-bold text-blue-600">{{ currency_format($totalCogs, restaurant()->currency_id) }}</p>
        </div>
    </div>

    <!-- Detailed Report Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('inventory::modules.reports.cogs.title') }}
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
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide dark:text-gray-400">{{ __('inventory::modules.reports.cogs.table.item') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('inventory::modules.reports.cogs.table.category') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('inventory::modules.reports.cogs.table.quantity_used') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('inventory::modules.reports.cogs.table.total_cost') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
                @foreach($reportData as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->product_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $categories->firstWhere('id', $item->inventory_item_category_id)?->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($item->total_quantity, 2) . ' ' . $item->item->unit->symbol ?? $item->item->unit->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency_format($item->total_cost, restaurant()->currency_id) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
