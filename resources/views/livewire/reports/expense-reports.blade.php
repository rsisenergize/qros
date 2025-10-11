<div>
    <div
        class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px">

            <li class="me-2">
                <a href="#" wire:click.prevent="$set('activeReport', 'outstandingPaymentReport')"
                    @class([
                        'inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeReport != 'outstandingPaymentReport',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                            $activeReport == 'outstandingPaymentReport',
                    ])>
                    @lang('modules.expenses.reports.outstandingPaymentReport')
                </a>
            </li>

            <li class="me-2">
                <a href="#" wire:click.prevent="$set('activeReport', 'expenseSummaryReport')"
                    @class([
                        'inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300',
                        'border-transparent' => $activeReport != 'expenseSummaryReport',
                        'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' =>
                            $activeReport == 'expenseSummaryReport',
                    ])>
                    @lang('modules.expenses.reports.expenseSummaryReport')
                </a>
            </li>

        </ul>
    </div>

    <div class="grid grid-cols-1 pt-6 dark:bg-gray-900">
        <div>
            @switch($activeReport)
                @case('outstandingPaymentReport')
                    @livewire('reports.outstanding-payment-report', ['reports' => $reports])
                @break

                @case('expenseSummaryReport')
                    @livewire('reports.expense-summary-report', ['reports' => $reports])
                @break

                @default
                    <p class="text-center text-gray-500 dark:text-gray-400">@lang('modules.reports.selectReport')</p>
            @endswitch
        </div>
    </div>
</div>
