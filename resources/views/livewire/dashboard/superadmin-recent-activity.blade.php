<div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            @lang('modules.dashboard.recentActivity')
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @lang('modules.dashboard.latestPlatformActivities')
        </p>
    </div>

    <!-- Activity Timeline -->
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            @foreach($recentActivities as $index => $activity)
                <li>
                    <div class="relative pb-8">
                        @if($index !== count($recentActivities) - 1)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                        @endif

                        <div class="relative flex space-x-3">
                            <div>
                                <span @class([
                                    'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800',
                                    'bg-blue-500' => $activity['color'] === 'blue',
                                    'bg-green-500' => $activity['color'] === 'green',
                                    'bg-purple-500' => $activity['color'] === 'purple',
                                    'bg-red-500' => $activity['color'] === 'red',
                                    'bg-yellow-500' => $activity['color'] === 'yellow',
                                    'bg-indigo-500' => $activity['color'] === 'indigo'
                                ])>
                                    @if($activity['icon'] === 'building-storefront')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'credit-card')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'check-circle')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'user')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </span>
                            </div>

                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $activity['title'] }}
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $activity['description'] }}
                                        </span>
                                    </p>
                                </div>

                                <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    @if($activity['amount'])
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ global_currency_format($activity['amount'], global_setting()->default_currency_id) }}
                                        </span>
                                        <br>
                                    @endif
                                    <time datetime="{{ $activity['time']->toISOString() }}">
                                        {{ $activity['time']->diffForHumans() }}
                                    </time>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Quick Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                {{ count($recentRestaurants) }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.newRestaurants')
            </div>
        </div>

        <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ count($recentPayments) }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.recentPayments')
            </div>
        </div>

        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ count($recentSubscriptions) }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.subscriptionUpdates')
            </div>
        </div>

        <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                {{ count($recentUsers) }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @lang('modules.dashboard.newUsers')
            </div>
        </div>
    </div>
</div>
