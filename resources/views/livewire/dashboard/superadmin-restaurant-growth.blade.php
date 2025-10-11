<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="flex items-center justify-between mb-4">
        <div class="flex-shrink-0">
            <span class="text-xl font-bold leading-none text-gray-900 sm:text-2xl dark:text-white">
                {{ $totalRestaurants }}
            </span>
            <h3 class="text-base font-light text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.totalRestaurants')
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
                @lang('modules.dashboard.newThisMonth')
            </h3>
        </div>
    </div>

    <!-- Growth Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                        @lang('modules.dashboard.newThisMonth')
                    </p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        {{ $newThisMonth }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">
                        @lang('modules.dashboard.growthRate')
                    </p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                        {{ round($percentChange, 1) }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Chart -->
    <div id="restaurant-growth-chart" class="mb-6"></div>

    <!-- Recent Restaurants Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
            @lang('modules.dashboard.recentRestaurants')
        </h4>
        <div class="space-y-3">
            @foreach($recentRestaurants as $restaurant)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-blue-500 rounded-lg flex items-center justify-center">
                            <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover rounded-lg">
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $restaurant->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $restaurant->country->countries_name ?? 'N/A' }} •
                                {{ $restaurant->package->package_name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $restaurant->created_at->diffForHumans() }}
                        </p>
                        <div class="flex items-center mt-1">
                            @if($restaurant->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    @lang('modules.dashboard.active')
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    @lang('modules.dashboard.inactive')
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @script
    <script>
        if (document.getElementById('restaurant-growth-chart')) {
            const chart = new ApexCharts(document.getElementById('restaurant-growth-chart'), getRestaurantGrowthChartOptions());
            chart.render();

            // init again when toggling dark mode
            document.addEventListener('dark-mode', function () {
                chart.updateOptions(getRestaurantGrowthChartOptions());
            });
        }

        function getRestaurantGrowthChartOptions() {
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
                    height: 200,
                    type: 'line',
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
                        opacityFrom: 0.6,
                        opacityTo: 0.1
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
                        name: "@lang('modules.dashboard.newRestaurants')",
                        data: [
                            @foreach ($growthData as $data)
                                {{ $data->new_restaurants }},
                            @endforeach
                        ],
                        color: '#10B981'
                    }
                ],
                markers: {
                    size: 6,
                    strokeColors: '#ffffff',
                    strokeWidth: 2,
                    hover: {
                        size: undefined,
                        sizeOffset: 3
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: [
                        @foreach ($growthData as $data)
                            "{{ \Carbon\Carbon::createFromFormat('Y-m', $data->month)->translatedFormat('M Y') }}",
                        @endforeach
                    ],
                    labels: {
                        style: {
                            colors: [chartColors.labelColor],
                            fontSize: '12px',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: {
                        color: chartColors.borderColor,
                    },
                    axisTicks: {
                        color: chartColors.borderColor,
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: [chartColors.labelColor],
                            fontSize: '12px',
                            fontWeight: 500,
                        },
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
