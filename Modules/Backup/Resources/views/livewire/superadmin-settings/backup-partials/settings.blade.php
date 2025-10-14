<!-- Settings Form -->
<form wire:submit.prevent="saveSettings" class="space-y-6">

    <!-- Backup Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Backup Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $isEnabled ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                        <svg class="w-4 h-4 {{ $isEnabled ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">@lang('backup::app.backupStatus')</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $isEnabled ? trans('backup::app.enabled') : trans('backup::app.disabled') }}</p>
                </div>
            </div>
        </div>

        <!-- Next Backup Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">@lang('backup::app.nextBackup')</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if($isEnabled && $nextBackupTime)
                            {{ $nextBackupTime->format('M j, g:i A') }}
                        @else
                            @lang('backup::app.notScheduled')
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Backups Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-purple-100 dark:bg-purple-900">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">@lang('backup::app.totalBackups')</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $backups->total() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Backup Schedule Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-orange-100 dark:bg-orange-900">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">@lang('backup::app.backupSchedule')</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if($isEnabled)
                            {{ ucfirst($frequency) }} @ {{ substr($backupTime, 0, 5) }}
                        @else
                            @lang('backup::app.notScheduled')
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enable/Disable Section -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">@lang('backup::app.enableScheduledBackups')</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">@lang('backup::app.automaticallyCreateDatabaseBackups')</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="isEnabled" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
            </label>
        </div>
    </div>

    <!-- Next Backup Information -->
    @if($isEnabled)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">@lang('backup::app.nextScheduledBackup')</h4>
                    <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                        @if($nextBackupTime)
                            <div class="flex items-center space-x-2">
                                <span class="font-medium">{{ $nextBackupTime->timezone(global_setting()->timezone)->format('M j, Y g:i A') }}</span>
                                <span class="text-xs">({{ $nextBackupTime->timezone(global_setting()->timezone)->diffForHumans() }})</span>
                            </div>
                            <div class="mt-1 text-xs">
                                <span class="font-medium">@lang('backup::app.cronExpression'):</span>
                                <code class="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded text-xs">{{ $cronExpression }}</code>
                            </div>
                        @else
                            <span class="text-gray-500 dark:text-gray-400">@lang('backup::app.notScheduled')</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Schedule Settings -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-label for="frequency" :value="trans('backup::app.backupFrequency')" />
            <x-select id="frequency" class="block mt-1 w-full" wire:model.live="frequency">
                @foreach($frequencyOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-select>
            <x-input-error for="frequency" class="mt-2" />
        </div>

        <div>
            <x-label for="backupTime" :value="trans('backup::app.backupTime')" />
            <x-input id="backupTime" class="block mt-1 w-full" type="time" wire:model.live="backupTime" />
            <x-input-error for="backupTime" class="mt-2" />
        </div>

        <div>
            <x-label for="storageLocation" :value="trans('backup::app.storageLocation')" />
            <x-select id="storageLocation" class="block mt-1 w-full" wire:model.live="storageLocation">
                @foreach($storageLocationOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-select>
            <x-input-error for="storageLocation" class="mt-2" />
        </div>
    </div>

    <!-- Retention Settings -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-label for="retentionDays" :value="trans('backup::app.retentionDays')" />
            <x-input id="retentionDays" class="block mt-1 w-full" type="number" min="1" max="365" wire:model.live="retentionDays" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('backup::app.howManyDaysToKeepBackups')</p>
            <x-input-error for="retentionDays" class="mt-2" />
        </div>

        <div>
            <x-label for="maxBackups" :value="trans('backup::app.maximumBackups')" />
            <x-input id="maxBackups" class="block mt-1 w-full" type="number" min="1" max="100" wire:model.live="maxBackups" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@lang('backup::app.maximumNumberOfBackupsToKeep')</p>
            <x-input-error for="maxBackups" class="mt-2" />
        </div>
    </div>

    <!-- File Backup Options -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <div class="flex items-center mb-3">
            <input id="includeFiles" type="checkbox" wire:model.live="includeFiles" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="includeFiles" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('backup::app.includeApplicationFiles')</label>
        </div>

        @if($includeFiles)
            <div class="ml-6 space-y-3">
                <!-- Modules Backup Option -->
                <div class="flex items-center">
                    <input id="includeModules" type="checkbox" wire:model.live="includeModules" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="includeModules" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">@lang('backup::app.includeModules')</label>
                </div>

                <!-- File Backup Information -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                    <div class="flex items-start space-x-2">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                @lang('backup::app.completeFileBackupInfo')
                            </p>
                            @if($includeModules)
                                <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                    <strong>@lang('backup::app.modulesIncluded'):</strong> @lang('backup::app.modulesWillBeIncludedInBackup')
                                </p>
                            @else
                                <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                    <strong>@lang('backup::app.modulesExcluded'):</strong> @lang('backup::app.modulesWillBeExcludedFromBackup')
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Live Backup Size Information -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3">@lang('backup::app.backupSizeInformation')</h4>

                <!-- Live Update Indicators -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('backup::app.estimatedBackupSize')</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">@lang('backup::app.perBackup')</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400" wire:loading.class="opacity-50">
                                    {{ $estimatedBackupSize }}
                                </p>
                                <div wire:loading class="text-xs text-blue-500">@lang('backup::app.calculating')</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('backup::app.totalStorageNeeded')</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">@lang('backup::app.forBackups') {{ $maxBackups }} backups</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600 dark:text-green-400" wire:loading.class="opacity-50">
                                    {{ $estimatedTotalStorage }}
                                </p>
                                <div wire:loading class="text-xs text-green-500">@lang('backup::app.updating')</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">@lang('backup::app.currentTotalBackupSize')</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">@lang('backup::app.existingBackups')</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                    {{ $currentTotalBackupSize }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Real-time Updates Info -->
                <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            @lang('backup::app.roughEstimatesBasedOnCurrentData')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end pt-4">
        <x-button type="submit" class="bg-blue-600 hover:bg-blue-700" wire:loading.attr="disabled" wire:loading.class="opacity-50">
            <span wire:loading.remove>@lang('backup::app.saveSettings')</span>
            <span wire:loading>@lang('backup::app.saving')</span>
        </x-button>
    </div>
</form>

@script
<script>
document.addEventListener('livewire:init', () => {
    // Handle max backups changes
    Livewire.on('maxBackupsUpdated', () => {
        console.log('Max backups updated, recalculating estimates...');
    });

    // Add real-time validation for backup time
    const backupTimeInput = document.getElementById('backupTime');
    if (backupTimeInput) {
        backupTimeInput.addEventListener('change', function() {
            const time = this.value;
            if (time) {
                // Ensure time is in correct format
                const timeParts = time.split(':');
                if (timeParts.length === 2) {
                    const hours = parseInt(timeParts[0]);
                    const minutes = parseInt(timeParts[1]);

                    if (hours >= 0 && hours <= 23 && minutes >= 0 && minutes <= 59) {
                        // Format time properly
                        const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
                        @this.set('backupTime', formattedTime);
                    }
                }
            }
        });
    }
});
</script>
@endscript
