<div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <nav class="-mb-px flex space-x-8" aria-label="Reports">
        <a href="{{ route('inventory.reports.usage') }}" 
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('inventory.reports.usage') 
                ? 'border-skin-base text-skin-base dark:text-skin-base' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            {{ __('inventory::modules.reports.usage.title') }}
        </a>

        <a href="{{ route('inventory.reports.forecasting') }}" 
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('inventory.reports.forecasting') 
                ? 'border-skin-base text-skin-base dark:text-skin-base' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            {{ __('inventory::modules.reports.forecasting.title') }}
        </a>

        <a href="{{ route('inventory.reports.turnover') }}" 
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('inventory.reports.turnover') 
                ? 'border-skin-base text-skin-base dark:text-skin-base' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            {{ __('inventory::modules.reports.turnover.title') }}
        </a>
        <a href="{{ route('inventory.reports.cogs') }}" 
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('inventory.reports.cogs') 
                ? 'border-skin-base text-skin-base dark:text-skin-base' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            {{ __('inventory::modules.reports.cogs.title') }}
        </a>
        <a href="{{ route('inventory.reports.profit-and-loss') }}" 
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('inventory.reports.profit-and-loss') 
                ? 'border-skin-base text-skin-base dark:text-skin-base' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            {{ __('inventory::modules.reports.profit_and_loss_report') }}
        </a>
    </nav>
</div> 