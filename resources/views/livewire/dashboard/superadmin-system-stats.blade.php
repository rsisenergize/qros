<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            @lang('modules.dashboard.platformOverview')
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @lang('modules.dashboard.comprehensivePlatformMetrics')
        </p>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">
                        @lang('modules.dashboard.totalRevenue')
                    </p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                        {{ global_currency_format($totalRevenue, global_setting()->default_currency_id) }}
                    </p>
                </div>
                <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Restaurants -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                        @lang('modules.dashboard.totalRestaurants')
                    </p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        {{ number_format($totalRestaurants) }}
                    </p>
                </div>
                <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Subscriptions -->
        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">
                        @lang('modules.dashboard.totalSubscriptions')
                    </p>
                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                        {{ number_format($totalSubscriptions) }}
                    </p>
                </div>
                <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600 dark:text-orange-400">
                        @lang('modules.dashboard.totalUsers')
                    </p>
                    <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                        {{ number_format($totalUsers) }}
                    </p>
                </div>
                <div class="p-2 bg-orange-100 dark:bg-orange-800 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Status & Restaurant Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subscription Status -->
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                @lang('modules.dashboard.subscriptionStatus')
            </h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('modules.dashboard.active')
                        </span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $activeSubscriptions }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('modules.dashboard.trial')
                        </span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $trialSubscriptions }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('modules.dashboard.inactive')
                        </span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $expiredSubscriptions }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Restaurant Status -->
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                @lang('modules.dashboard.restaurantStatus')
            </h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('modules.dashboard.active')
                        </span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $activeRestaurants }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('modules.dashboard.inactive')
                        </span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $inactiveRestaurants }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('modules.dashboard.monthlyGrowth')
                        </span>
                    </div>
                    <span @class([
                        'text-lg font-semibold',
                        'text-green-600 dark:text-green-400' => $monthlyGrowth > 0,
                        'text-red-600 dark:text-red-400' => $monthlyGrowth < 0,
                        'text-gray-900 dark:text-white' => $monthlyGrowth == 0
                    ])>
                        {{ round($monthlyGrowth, 1) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
