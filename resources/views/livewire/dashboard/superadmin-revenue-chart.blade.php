<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="flex items-center justify-between mb-4">
        <div class="flex-shrink-0">
            <span class="text-xl font-bold leading-none text-gray-900 sm:text-2xl dark:text-white">
                {{ global_currency_format($monthlyRevenue, global_setting()->default_currency_id) }}
            </span>
            <h3 class="text-base font-light text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.platformRevenueThisMonth')
            </h3>
        </div>
        <div class="flex flex-col justify-end">
            <div @class([
                "flex justify-end text-sm",
                'text-green-500 dark:text-green-400' => ($percentChange > 0),
                'text-red-600 dark:text-red-600' => ($percentChange < 0)
            ])>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    @if ($percentChange > 0)
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z"></path>
                    @endif
                    @if ($percentChange < 0)
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z"></path>
                    @endif
                </svg>
                {{ round($percentChange, 2) }}%
            </div>
            <h3 class="text-sm font-light text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.sincePreviousMonth')
            </h3>
        </div>
    </div>

    <!-- Subscription Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $subscriptionStats['total_subscriptions'] }}
                </div>
                <div class="text-sm text-blue-600 dark:text-blue-400">
                    @lang('modules.dashboard.totalSubscriptions')
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ $subscriptionStats['active_subscriptions'] }}
                </div>
                <div class="text-sm text-green-600 dark:text-green-400">
                    @lang('modules.dashboard.activeSubscriptions')
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $subscriptionStats['trial_subscriptions'] }}
                </div>
                <div class="text-sm text-yellow-600 dark:text-yellow-400">
                    @lang('modules.dashboard.trialSubscriptions')
                </div>
            </div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ $subscriptionStats['expired_subscriptions'] }}
                </div>
                <div class="text-sm text-red-600 dark:text-red-400">
                    @lang('modules.dashboard.inactiveSubscriptions')
                </div>
            </div>
        </div>
    </div>

    <div id="superadmin-revenue-chart" class="mb-6"></div>

    <!-- Top Paying Restaurants Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
            @lang('modules.dashboard.topPayingRestaurants')
        </h4>
        <div class="space-y-3">
            @foreach($topRestaurants as $restaurant)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">
                                {{ $loop->iteration }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $restaurant->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $restaurant->payment_count }} @lang('modules.dashboard.payments')
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ global_currency_format($restaurant->total_payments, global_setting() && global_setting()->default_currency_id ? global_setting()->default_currency_id : 1) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @script
    <script>
        if (document.getElementById('superadmin-revenue-chart')) {
            const chart = new ApexCharts(document.getElementById('superadmin-revenue-chart'), getSuperadminRevenueChartOptions());
            chart.render();

            // init again when toggling dark mode
            document.addEventListener('dark-mode', function () {
                chart.updateOptions(getSuperadminRevenueChartOptions());
            });
        }

        function getSuperadminRevenueChartOptions() {
            let chartColors = {}

            if (document.documentElement.classList.contains('dark')) {
                chartColors = {
                    borderColor: '#374151',
                    labelColor: '#9CA3AF',
                    opacityFrom: 0,
                    opacityTo: 0.15,
                };
            } else {
                chartColors = {
                    borderColor: '#F3F4F6',
                    labelColor: '#6B7280',
                    opacityFrom: 0.45,
                    opacityTo: 0,
                }
            }

            return {
                chart: {
                    height: 300,
                    type: 'area',
                    fontFamily: 'Inter, sans-serif',
                    foreColor: chartColors.labelColor,
                    toolbar: {
                        show: false
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        enabled: true,
                        opacityFrom: chartColors.opacityFrom,
                        opacityTo: chartColors.opacityTo
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif',
                    },
                },
                grid: {
                    show: true,
                    borderColor: chartColors.borderColor,
                    strokeDashArray: 1,
                    padding: {
                        left: 35,
                        bottom: 15
                    }
                },
                series: [
                    {
                        name: "@lang('modules.dashboard.platformRevenue')",
                        data: [
                            @foreach ($revenueData as $data)
                                {{ $data->total_revenue }},
                            @endforeach
                        ],
                        color: '#3B82F6'
                    }
                ],
                markers: {
                    size: 5,
                    strokeColors: '#ffffff',
                    hover: {
                        size: undefined,
                        sizeOffset: 3
                    }
                },
                xaxis: {
                    categories: [
                        @foreach ($revenueData as $data)
                            "{{ \Carbon\Carbon::createFromFormat('Y-m', $data->month)->translatedFormat('M Y') }}",
                        @endforeach
                    ],
                    labels: {
                        style: {
                            colors: [chartColors.labelColor],
                            fontSize: '14px',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        color: chartColors.borderColor,
                    },
                    axisTicks: {
                        color: chartColors.borderColor,
                    },
                    crosshairs: {
                        show: true,
                        position: 'back',
                        stroke: {
                            color: chartColors.borderColor,
                            width: 1,
                            dashArray: 10,
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: [chartColors.labelColor],
                            fontSize: '14px',
                            fontWeight: 500,
                        },
                        formatter: function (value) {
                            return '{{ global_setting() && global_setting()->defaultCurrency ? global_setting()->defaultCurrency->currency_symbol : '$' }}' + value;
                        }
                    },
                },
                legend: {
                    fontSize: '14px',
                    fontWeight: 500,
                    fontFamily: 'Inter, sans-serif',
                    labels: {
                        colors: [chartColors.labelColor]
                    },
                    itemMargin: {
                        horizontal: 10
                    }
                },
                responsive: [
                    {
                        breakpoint: 1024,
                        options: {
                            xaxis: {
                                labels: {
                                    show: false
                                }
                            }
                        }
                    }
                ]
            }
        }
    </script>
    @endscript
</div>
