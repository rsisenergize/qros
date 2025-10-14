<div class="space-y-6">
    <!-- Backup Health Score -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">@lang('backup::app.backupHealthScore')</h3>
            <div class="flex items-center space-x-2">
                <button wire:click="loadIntelligenceData" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    @lang('backup::app.refresh')
                </button>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="relative">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-{{ $backupHealthScore['status'] === 'excellent' ? 'green' : ($backupHealthScore['status'] === 'good' ? 'blue' : ($backupHealthScore['status'] === 'fair' ? 'yellow' : 'red')) }}-500" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{ $backupHealthScore['score'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $backupHealthScore['score'] }}</span>
                    </div>
                </div>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white capitalize">{{ $backupHealthScore['status'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('backup::app.backupHealthDescription')</p>
                @if(!empty($backupHealthScore['issues']))
                    <div class="mt-2">
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">@lang('backup::app.issuesFound'):</p>
                        <ul class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @foreach($backupHealthScore['issues'] as $issue)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $issue }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- AI Recommendations -->
    @if(!empty($recommendations))
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">@lang('backup::app.aiRecommendations')</h3>
            <div class="space-y-4">
                @foreach($recommendations as $recommendation)
                    <div class="flex items-start space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-600
                        @if($recommendation['type'] === 'warning') bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800
                        @elseif($recommendation['type'] === 'recommendation') bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800
                        @else bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 @endif">
                        <div class="flex-shrink-0">
                            @if($recommendation['icon'] === 'exclamation-triangle')
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($recommendation['icon'] === 'clock')
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($recommendation['icon'] === 'calendar')
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($recommendation['icon'] === 'trending-up')
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($recommendation['icon'] === 'document')
                                <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $recommendation['title'] }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $recommendation['message'] }}</p>
                            @if(isset($recommendation['action']))
                                <div class="mt-3">
                                    <button wire:click="applyRecommendation('{{ $recommendation['action'] }}', {{ json_encode($recommendation) }})"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white
                                        @if($recommendation['type'] === 'warning') bg-red-600 hover:bg-red-700
                                        @elseif($recommendation['type'] === 'recommendation') bg-blue-600 hover:bg-blue-700
                                        @else bg-yellow-600 hover:bg-yellow-700 @endif
                                        focus:outline-none focus:ring-2 focus:ring-offset-2
                                        @if($recommendation['type'] === 'warning') focus:ring-red-500
                                        @elseif($recommendation['type'] === 'recommendation') focus:ring-blue-500
                                        @else focus:ring-yellow-500 @endif">
                                        @if($recommendation['action'] === 'create_backup')
                                            @lang('backup::app.createBackupNow')
                                        @elseif($recommendation['action'] === 'change_backup_time')
                                            @lang('backup::app.applyTimeChange')
                                        @elseif($recommendation['action'] === 'increase_frequency')
                                            @lang('backup::app.increaseFrequency')
                                        @else
                                            @lang('backup::app.applyRecommendation')
                                        @endif
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Data Change Insights -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">@lang('backup::app.dataChangeInsights')</h3>

        @if($dataChangeInsights['total_changes'] > 0)
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        @lang('backup::app.sinceLastBackup'): <span class="font-medium">{{ $dataChangeInsights['days_since_backup'] }}</span>
                    </p>
                    <div class="flex items-center space-x-2">
                        @if($dataChangeInsights['backup_recommended'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                @lang('backup::app.backupRecommended')
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                @lang('backup::app.minimalChanges')
                            </span>
                        @endif
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $dataChangeInsights['recommendation_reason'] }}</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($dataChangeInsights['changes'] as $type => $change)
                    @if($change['count'] > 0)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                            <div class="flex items-center justify-center mb-2">
                                @if($change['icon'] === 'shopping-cart')
                                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                                    </svg>
                                @elseif($change['icon'] === 'users')
                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($change['icon'] === 'user')
                                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($change['icon'] === 'building')
                                    <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2h6v4H7V6zm8 8v2h1v-2h-1zm-2-8v2h1V6h-1zM5 14v2H4v-2h1z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($change['icon'] === 'document')
                                    <svg class="w-6 h-6 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $change['count'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $change['label'] }}</div>
                            @if($type === 'files' && isset($change['size_formatted']))
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $change['size_formatted'] }}</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            @if($dataChangeInsights['backup_recommended'])
                <div class="mt-4 text-center">
                    <button wire:click="openCreateBackupModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                            @lang('backup::app.createBackupNow')
                    </button>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">@lang('backup::app.noDataChanges')</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $dataChangeInsights['message'] ?? 'No data changes detected since last backup.' }}</p>
            </div>
        @endif
    </div>
</div>
