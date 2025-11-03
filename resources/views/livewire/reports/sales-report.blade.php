<div>
    <!-- Header Section -->
    <div class="p-4 bg-white dark:bg-gray-800">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('menu.salesReport')</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                @lang('modules.report.salesReportMessage')
                @php
                    $formattedStartTime = \Carbon\Carbon::parse($startTime)->format('h:i A');
                    $formattedEndTime = \Carbon\Carbon::parse($endTime)->format('h:i A');
                @endphp
                <strong>
                    ({{ $startDate === $endDate
                        ? __('modules.report.salesDataFor') . " $startDate, " . __('modules.report.timePeriod') . " $formattedStartTime - $formattedEndTime"
                        : __('modules.report.salesDataFrom') . " $startDate " . __('app.to') . " $endDate, " . __('modules.report.timePeriodEachDay') . " $formattedStartTime - $formattedEndTime" }})
                </strong>
            </p>
        </div>

        <div class="grid grid-cols-1 gap-3 mb-4 sm:grid-cols-2 lg:grid-cols-6">
            <!-- Total Sales Card -->
            <div class="p-3 bg-skin-base/10 rounded-xl shadow-sm dark:bg-skin-base/10 border border-skin-base/30 dark:border-skin-base/40">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-skin-base dark:text-skin-base">@lang('modules.report.totalSales')</h3>
                    <div class="p-1.5 bg-skin-base/10 rounded-lg dark:bg-skin-base/10">
                        <svg class="w-3.5 h-3.5 text-skin-base dark:text-skin-base" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 13.75c0 .97.75 1.75 1.67 1.75h1.88c.8 0 1.45-.68 1.45-1.53 0-.91-.4-1.24-.99-1.45l-3.01-1.05c-.59-.21-.99-.53-.99-1.45 0-.84.65-1.53 1.45-1.53h1.88c.92 0 1.67.78 1.67 1.75M12 7.5v9"/><path d="M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2m10 4V2h-4m-1 5 5-5"/></g></svg>
                    </div>
                </div>
                <p class="text-xl break-words font-bold text-skin-base dark:text-skin-base mb-2">
                    {{ currency_format($menuItems->sum('total_amount'), $currencyId) }}
                </p>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between rounded-lg bg-skin-base/10 p-2 dark:bg-skin-base/10">
                        <span class="text-xs font-medium text-skin-base dark:text-skin-base">
                            @lang('modules.report.orders')
                        </span>
                        <span class="text-xs font-bold text-skin-base dark:text-skin-base">
                            {{ $menuItems->sum('total_orders') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Cash Card -->
            <div class="p-3 bg-emerald-50 rounded-xl shadow-sm dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.traditionalPayments')</h3>
                        <div class="p-1.5 bg-emerald-100 text-emerald-600 rounded-lg dark:bg-emerald-900/20 dark:text-emerald-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ currency_format($menuItems->sum('cash_amount') + $menuItems->sum('card_amount') + $menuItems->sum('upi_amount') + $menuItems->sum('bank_transfer_amount'), $currencyId) }}
                    </p>
                    <div class="space-y-1.5">
                        @php
                            $traditionalPayments = [
                                'cash' => $menuItems->sum('cash_amount'),
                                'card' => $menuItems->sum('card_amount'),
                                'upi' => $menuItems->sum('upi_amount'),
                                'bank_transfer' => $menuItems->sum('bank_transfer_amount')
                            ];
                        @endphp

                        @foreach($traditionalPayments as $method => $amount)
                            <div class="flex items-center justify-between rounded-lg bg-emerald-100/50 p-2 dark:bg-emerald-900/20">
                                <span class="text-xs font-medium text-emerald-700 dark:text-emerald-100">
                                    @lang("modules.order.{$method}")
                                </span>
                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ currency_format($amount, $currencyId) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
            </div>

            <!-- Online Payments Card -->
            <div class="p-3 bg-emerald-50 rounded-xl shadow-sm dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.paymentGateways')</h3>
                        <div class="p-1.5 bg-emerald-100 text-emerald-600 rounded-lg dark:bg-emerald-900/20 dark:text-emerald-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ currency_format($menuItems->sum('razorpay_amount') + $menuItems->sum('stripe_amount') + $menuItems->sum('flutterwave_amount'), $currencyId) }}
                    </p>
                    <div class="space-y-1.5">
                        @php
                            $paymentMethods = [
                                'razorpay' => [
                                    'status' => $paymentGateway->razorpay_status,
                                    'amount' => $menuItems->sum('razorpay_amount')
                                ],
                                'stripe' => [
                                    'status' => $paymentGateway->stripe_status,
                                    'amount' => $menuItems->sum('stripe_amount')
                                ],
                                'flutterwave' => [
                                    'status' => $paymentGateway->flutterwave_status,
                                    'amount' => $menuItems->sum('flutterwave_amount')
                                ]
                            ];
                        @endphp

                        @foreach($paymentMethods as $method => $details)
                            @if($details['status'])
                            <div class="flex items-center justify-between rounded-lg bg-emerald-100/50 p-2 dark:bg-emerald-900/20">
                                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-100">
                                        @lang("modules.order.{$method}")
                                    </span>
                                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                        {{ currency_format($details['amount'], $currencyId) }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
            </div>

            <!-- Additional Amounts Card -->
            <div class="p-3 bg-rose-50 rounded-xl shadow-sm dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.additionalAmounts')</h3>
                        <div class="p-1.5 bg-rose-100 rounded-lg dark:bg-rose-800/50">
                            <svg class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/></svg>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        @php
                            $additionalAmounts = [
                                'totalCharges' => [
                                    'label' => 'modules.report.totalCharges',
                                    'amount' => $charges->sum(fn($charge) => $menuItems->sum(fn($item) => $item['charges'][$charge->charge_name] ?? 0))
                                ],
                                'totalTaxes' => [
                                    'label' => 'modules.report.totalTaxes',
                                    'amount' => $menuItems->sum('total_tax_amount')
                                ],
                                'discount' => [
                                    'label' => 'modules.order.discount',
                                    'amount' => $menuItems->sum('discount_amount')
                                ],
                                'tip' => [
                                    'label' => 'modules.order.tip',
                                    'amount' => $menuItems->sum('tip_amount')
                                ]
                            ];
                        @endphp

                        @foreach($additionalAmounts as $key => $data)
                            <div class="flex items-center justify-between rounded-lg bg-rose-100/50 p-2 dark:bg-rose-900/20">
                                <span class="text-xs font-medium text-rose-700 dark:text-rose-200">
                                    @lang($data['label'])
                                </span>
                                <span class="text-xs font-bold text-rose-800 dark:text-rose-200">
                                    {{ currency_format($data['amount'], $currencyId) }}
                                </span>
                            </div>
                        @endforeach
                </div>
            </div>

            <!-- Tax Breakdown Card -->
            <div class="p-3 bg-purple-50 rounded-xl shadow-sm dark:bg-purple-900/10 border border-purple-100 dark:border-purple-800">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-800 dark:text-gray-200">@lang('modules.report.taxBreakdown')</h3>
                    <div class="p-1.5 bg-purple-100 rounded-lg dark:bg-purple-800/50">
                        <svg class="w-3.5 h-3.5 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between rounded-lg bg-purple-100/50 p-2 dark:bg-purple-900/20">
                        <span class="text-xs font-medium text-purple-700 dark:text-purple-200">
                            @lang('modules.report.taxMode')
                        </span>
                        <span class="text-xs font-bold text-purple-800 dark:text-purple-200 capitalize">
                            {{ $taxMode }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-purple-100/50 p-2 dark:bg-purple-900/20">
                        <span class="text-xs font-medium text-purple-700 dark:text-purple-200">
                            @lang('modules.report.totalTaxCollection')
                        </span>
                        <span class="text-xs font-bold text-purple-800 dark:text-purple-200">
                            {{ currency_format($menuItems->sum('total_tax_amount'), $currencyId) }}
                        </span>
                    </div>

                    @foreach($allTaxes as $taxName => $taxData)
                        <div class="flex items-center justify-between rounded-lg bg-purple-100/50 p-2 dark:bg-purple-900/20">
                            <span class="text-xs font-medium text-purple-700 dark:text-purple-200">
                                {{ $taxName }} ({{ number_format($taxData['percent'], 2) }}%)
                            </span>
                            <span class="text-xs font-bold text-purple-800 dark:text-purple-200">
                                {{ currency_format($taxData['total_amount'], $currencyId) }}
                            </span>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- Outstanding Payments Card -->
            <div class="p-3 bg-orange-50 rounded-xl shadow-sm dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-orange-700 dark:text-orange-200">@lang('modules.report.outstandingPayments')</h3>
                    <div class="p-1.5 bg-orange-100 rounded-lg dark:bg-orange-800/50">
                        <svg class="w-3.5 h-3.5 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xl break-words font-bold text-orange-700 dark:text-orange-200 mb-2">
                    {{ currency_format($menuItems->sum('outstanding_amount'), $currencyId) }}
                </p>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between rounded-lg bg-orange-100 p-2 dark:bg-orange-100">
                        <span class="text-xs font-medium text-orange-800 dark:text-orange-300">
                            @lang('modules.report.outstandingOrders')
                        </span>
                        <span class="text-xs font-bold text-orange-800 dark:text-orange-300">
                            {{ $menuItems->sum('outstanding_orders') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="flex flex-wrap justify-between items-center gap-4 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
            <div class="lg:flex items-center mb-4 sm:mb-0">
                <form  action="#" method="GET">

                    <div class="lg:flex gap-2 items-center">
                        <x-select id="dateRangeType" class="block w-full sm:w-fit mb-2 lg:mb-0" wire:model="dateRangeType" wire:change="setDateRange">
                            <option value="today">@lang('app.today')</option>
                            <option value="currentWeek">@lang('app.currentWeek')</option>
                            <option value="lastWeek">@lang('app.lastWeek')</option>
                            <option value="last7Days">@lang('app.last7Days')</option>
                            <option value="currentMonth">@lang('app.currentMonth')</option>
                            <option value="lastMonth">@lang('app.lastMonth')</option>
                            <option value="currentYear">@lang('app.currentYear')</option>
                            <option value="lastYear">@lang('app.lastYear')</option>
                        </x-select>

                        <div id="date-range-picker" date-rangepicker class="flex items-center w-full">
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20zM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2"/></svg>
                                </div>
                                <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                            </div>
                            <span class="mx-4 text-gray-500 dark:text-gray-100">@lang('app.to')</span>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20zM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2"/></svg>
                                </div>
                                <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                            </div>
                        </div>

                        <div class="lg:flex items-center gap-2 ms-2">
                            <div class="w-full max-w-[7rem]">
                                <label for="start-time" class="sr-only">@lang('modules.reservation.timeStart'):</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 end-0 top-0 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" width="24" height="24" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 7.5a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0m7 0V3h1v4.293l2.854 2.853-.708.708-3-3A.5.5 0 0 1 7 7.5" fill="currentColor"/></svg>
                                    </div>
                                    <x-input id="start-time" type="time" wire:model.live.debounce.500ms="startTime" />
                                </div>
                            </div>
                            <span class="mx-2 text-gray-500 dark:text-gray-100 w-10 text-center">@lang('app.to')</span>
                            <div class="w-full max-w-[7rem]">
                                <label for="end-time" class="sr-only">@lang('modules.reservation.timeEnd'):</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 end-0 top-0 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" width="24" height="24" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 7.5a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0m7 0V3h1v4.293l2.854 2.853-.708.708-3-3A.5.5 0 0 1 7 7.5" fill="currentColor"/></svg>
                                    </div>
                                    <x-input id="end-time" type="time" wire:model.live.debounce.500ms="endTime" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="inline-flex items-center gap-2 w-1/2 sm:w-auto ms-2">
                <a href="javascript:;" wire:click='exportReport'
                class="inline-flex items-center  w-1/2 px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586z" clip-rule="evenodd"/></svg>
                    @lang('app.export')
                </a>

                <select wire:model.live="selectedWaiter" wire:change="filterWaiter" class="px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-4 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                    <option value="">@lang('app.reportByWaiter')</option>
                    @foreach($waiters ?? [] as $waiter)
                        <option value="{{ $waiter->id }}">{{ $waiter->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 p-4">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th class="p-4 text-xs font-medium tracking-wider text-left text-gray-600 uppercase dark:text-gray-300">
                @lang('app.date')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.report.totalOrders')
                </th>

                <!-- Charges Column Group -->
                @if(count($charges) > 0)
                <th colspan="{{ count($charges) }}" class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">
                    @lang('modules.order.extraCharges')
                </th>
                @endif

                <!-- Taxes Column Group -->
                @if(count($taxes) > 0)
                <th colspan="{{ count($taxes) + 1 }}" class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300 bg-red-50 dark:bg-red-900/20">
                    @lang('modules.order.taxes') (@lang('modules.report.fromActualBreakdown'))
                </th>
                @endif

                <!-- Payment Methods Column Group -->
                <th colspan="{{ 4 + collect(['stripe', 'razorpay', 'flutterwave'])->filter(fn($method) => isset($paymentGateway) && $paymentGateway->{"{$method}_status"})->count() }}" class="p-4 text-xs font-medium tracking-wider text-center text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.report.paymentMethods')
                </th>

                <!-- Due Payment Column -->
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-orange-50 dark:bg-orange-900/20">
                    @lang('modules.order.due')
                </th>

                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.deliveryFee')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.discount')
                </th>
                <th   class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.tip')
                </th>
                <th class="p-4 text-xs font-bold tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.total')
                </th>
                <th class="p-4 text-xs font-bold tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                @lang('modules.order.total')
                </th>
            </tr>
            <tr>
                <th></th>
                <th></th>

                <!-- Charges Subheaders -->
                @foreach ($charges as $charge)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20">
                    {{ $charge->charge_name }}
                </th>
                @endforeach

                <!-- Taxes Subheaders -->
                @foreach ($taxes as $tax)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-red-50 dark:bg-red-900/20">
                    {{ $tax->tax_name }} ({{ $tax->tax_percent }}%)
                </th>
                @endforeach

                <!-- Total Tax Amount Column -->
                @if(count($taxes) > 0)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-red-50 dark:bg-red-900/20">
                    @lang('modules.report.totalTaxAmount')
                </th>
                @endif

                <!-- Payment Methods Subheaders -->
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.cash')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.upi')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.card')
                </th>
                <th class=" py-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                @lang('modules.order.bank_transfer')
                </th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-orange-50 dark:bg-orange-900/20">

                </th>
                @if($paymentGateway->razorpay_status)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.order.razorpay')
                </th>
                @endif
                @if($paymentGateway->stripe_status)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.order.stripe')
                </th>
                @endif
                @if($paymentGateway->flutterwave_status)
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300 bg-green-50 dark:bg-green-900/20">
                    @lang('modules.order.flutterwave')
                </th>
                @endif
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th class="p-4 text-xs font-medium tracking-wider text-right text-gray-600 uppercase dark:text-gray-300">
                    @lang('modules.order.totalExcludingTip')
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            @forelse ($menuItems as $item)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                {{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}
                </td>
                <td class="p-4 text-sm text-center text-gray-900 dark:text-white">
                {{ $item['total_orders'] }}
                </td>

                @foreach ($charges as $charge)
                <td class="p-4 text-sm font-normal text-right text-gray-900 dark:text-gray-100 bg-blue-50/50 dark:bg-blue-900/10">
                {{ currency_format($item['charges'][$charge->charge_name] ?? 0, $currencyId) }}
                </td>
                @endforeach

                @foreach ($taxes as $tax)
                <td class="p-4 text-sm font-normal text-right text-gray-900 dark:text-gray-100 bg-red-50/50 dark:bg-red-900/10">
                    {{ currency_format($item['taxes'][$tax->tax_name] ?? 0, $currencyId) }}
                </td>
                @endforeach

                @if(count($taxes) > 0)
                <td class="p-4 text-sm font-normal text-right text-gray-900 dark:text-gray-100 bg-red-50/50 dark:bg-red-900/10">
                    {{ currency_format($item['total_tax_amount'], $currencyId) }}
                </td>
                @endif

                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['cash_amount'], $currencyId) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['upi_amount'], $currencyId) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['card_amount'], $currencyId) }}
                </td>
                <td class="px-5 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                {{ currency_format($item['bank_transfer_amount'], $currencyId) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-orange-50/50 dark:bg-orange-900/10">
                    {{ currency_format($item['outstanding_amount'], $currencyId) }}
                </td>
                @if($paymentGateway->razorpay_status)
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                    {{ currency_format($item['razorpay_amount'], $currencyId) }}
                </td>
                @endif
                @if($paymentGateway->stripe_status)
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                    {{ currency_format($item['stripe_amount'], $currencyId) }}
                </td>
                @endif
                @if($paymentGateway->flutterwave_status)
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white bg-green-50/50 dark:bg-green-900/10">
                    {{ currency_format($item['flutterwave_amount'], $currencyId) }}
                </td>
                @endif
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white ">
                    {{ currency_format($item['delivery_fee'], $currencyId) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white">
                    {{ currency_format($item['discount_amount'], $currencyId) }}
                </td>
                <td class="p-4 text-sm text-right text-gray-900 dark:text-white">
                {{ currency_format($item['tip_amount'], $currencyId) }}
                </td>
                <td class="p-4 text-sm font-bold text-right text-gray-900 dark:text-white">
                {{ currency_format($item['total_amount'], $currencyId) }}
                </td>
                <td class="p-4 text-sm font-bold text-right text-gray-900 dark:text-white">
                {{ currency_format($item['total_excluding_tip'], $currencyId) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="p-4 text-sm text-center text-gray-500 dark:text-gray-400">
                @lang('messages.noItemAdded')
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @script
    <script>
        const datepickerEl1 = document.getElementById('datepicker-range-start');

        datepickerEl1.addEventListener('changeDate', (event) => {
            $wire.dispatch('setStartDate', { start: datepickerEl1.value });
        });

        const datepickerEl2 = document.getElementById('datepicker-range-end');

        datepickerEl2.addEventListener('changeDate', (event) => {
            $wire.dispatch('setEndDate', { end: datepickerEl2.value });
        });
    </script>
    @endscript
</div>
