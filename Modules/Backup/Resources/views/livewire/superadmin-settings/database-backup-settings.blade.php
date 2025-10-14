<div wire:id="database-backup-settings">
    <div class="mx-4 p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">

        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">@lang('backup::app.databaseBackupSettings')</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">@lang('backup::app.manageDatabaseBackupsAndSettings')</p>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button wire:click="switchTab('settings')" onclick="updateUrl('settings')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'settings' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        @lang('backup::app.settings')
                    </div>
                </button>

                <button wire:click="switchTab('history')" onclick="updateUrl('history')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'history' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @lang('backup::app.history')
                    </div>
                </button>

                <button wire:click="switchTab('health')" onclick="updateUrl('health')"
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'health' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        @lang('backup::app.health')
                    </div>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            @if($activeTab === 'settings')
                @include('backup::livewire.superadmin-settings.backup-partials.settings')
            @elseif($activeTab === 'history')
                @include('backup::livewire.superadmin-settings.backup-partials.history')
            @elseif($activeTab === 'health')
                @include('backup::livewire.superadmin-settings.backup-partials.health')
            @endif
        </div>
    </div>
</div>

@script
<script>
console.log('=== Database Backup Settings Script Loading ===');

// Function to update URL when tab is clicked
window.updateUrl = function(tabName) {
    console.log('updateUrl called with:', tabName);

    // Update URL immediately
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('tab', 'backup');
    currentUrl.searchParams.set('subtab', tabName);

    console.log('Updating URL to:', currentUrl.toString());
    window.history.pushState({}, '', currentUrl.toString());
};

document.addEventListener('livewire:load', function () {
    console.log('Livewire loaded, setting up URL update listener');

    // Listen for URL update events from Livewire
    Livewire.on('url-updated', (event) => {
        console.log('url-updated event received:', event);

        // Update browser URL without page reload
        if (event.url) {
            console.log('Updating URL to:', event.url);
            window.history.pushState({}, '', event.url);
        }
    });
});

console.log('=== Database Backup Settings Script Loaded ===');
</script>


@endscript
