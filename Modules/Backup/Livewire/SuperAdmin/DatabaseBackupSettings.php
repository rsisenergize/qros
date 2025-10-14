<?php

namespace Modules\Backup\Livewire\SuperAdmin;

use Modules\Backup\Models\DatabaseBackup;
use Modules\Backup\Models\DatabaseBackupSetting;
use Modules\Backup\Services\BackupIntelligenceService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\Backup\Console\Commands\CreateDatabaseBackup;

class DatabaseBackupSettings extends Component
{
    use LivewireAlert, WithPagination;

    public $settings;
    public $isEnabled = false;
    public $frequency = 'daily';
    public $backupTime = '02:00:00';
    public $retentionDays = 30;
    public $maxBackups = 10;
    public $storageLocation = 'local';
    public $storageConfig = [];

    // File backup properties
    public $includeFiles = false;
    public $includeModules = false;

    // Live calculation properties
    public $estimatedBackupSize = '65-70 MB';
    public $estimatedTotalStorage = '650-700 MB';
    public $currentTotalBackupSize = '0 B';

    public $showCreateBackupModal = false;
    public $showDeleteBackupModal = false;
    public $isCreatingBackup = false;
    public $backupToDelete = null;

    // Tab navigation
    public $activeTab = 'settings';

    // Intelligence features
    public $recommendations = [];
    public $dataChangeInsights = [];
    public $backupHealthScore = [];

    // Health check properties
    public $healthIssues = [];
    public $healthCheckCompleted = false;
    public $isRunningHealthCheck = false;
    public $syncResults = [];
    public $isSyncingBackups = false;

    protected $listeners = [
        'refreshBackups' => 'refreshBackups',
        'maxBackupsUpdated' => 'updateBackupEstimates'
    ];

    // Validation rules
    protected $rules = [
        'isEnabled' => 'boolean',
        'frequency' => 'required|in:daily,weekly,monthly',
        'backupTime' => 'required|date_format:H:i:s',
        'retentionDays' => 'required|integer|min:1|max:365',
        'maxBackups' => 'required|integer|min:1|max:100',
        'storageLocation' => 'required|in:local,storage_setting',
        'storageConfig' => 'array',
        'includeFiles' => 'boolean',
        'includeModules' => 'boolean',
    ];

    private $intelligenceService;

    public function boot(BackupIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function openCreateBackupModal()
    {
        $this->showCreateBackupModal = true;
    }

    public function closeCreateBackupModal()
    {
        $this->showCreateBackupModal = false;
    }

    public function mount()
    {
        // Get active tab from URL parameter, handle 'backup' parameter
        $tabParam = request()->get('tab', 'settings');
        $subTabParam = request()->get('subtab', 'settings');

        // If we're in the backup section (tab=backup), use the subtab parameter
        if ($tabParam === 'backup') {
            $this->activeTab = $subTabParam;
        } else {
            $this->activeTab = $tabParam;
        }

        $this->loadSettings();
        $this->loadIntelligenceData();

        // Ensure maxBackups is an integer
        $this->maxBackups = intval($this->maxBackups);

        $this->updateBackupEstimates();

        // Auto-sync backups when viewing history page
        if ($this->activeTab === 'history') {
            $this->autoSyncBackupsFromStorage();
        }
    }

    /**
     * Update backup estimates based on current settings
     */
    public function updateBackupEstimates()
    {
        // Get current backup statistics
        $recentBackups = DatabaseBackup::completed()->orderBy('created_at', 'desc')->limit(5)->get();

        if ($recentBackups->isEmpty()) {
            // Use default estimates when no backups exist
            if ($this->includeFiles) {
                if ($this->includeModules) {
                    $this->estimatedBackupSize = '80-90 MB'; // With modules
                } else {
                    $this->estimatedBackupSize = '65-70 MB'; // Without modules
                }
            } else {
                $this->estimatedBackupSize = '0-1 MB'; // Database only
            }
        } else {
            // Calculate average size from recent backups
            $avgSize = $recentBackups->avg('file_size');
            $avgSizeMB = round($avgSize / 1024 / 1024, 1);
            $this->estimatedBackupSize = $avgSizeMB . ' MB';
        }

        // Calculate total storage needed
        $this->calculateTotalStorage();

        // Get current total backup size
        $this->currentTotalBackupSize = DatabaseBackup::getTotalBackupSize();
    }

    /**
     * Calculate total storage needed based on max backups
     */
    public function calculateTotalStorage()
    {
        // Ensure maxBackups is an integer
        $maxBackups = intval($this->maxBackups);

        // Extract number from estimated size
        if (preg_match('/(\d+(?:\.\d+)?)/', $this->estimatedBackupSize, $matches)) {
            $sizeMB = floatval($matches[1]);
            $totalMB = $sizeMB * $maxBackups;

            if ($totalMB >= 1024) {
                $this->estimatedTotalStorage = round($totalMB / 1024, 1) . ' GB';
            } else {
                $this->estimatedTotalStorage = round($totalMB, 1) . ' MB';
            }
        } else {
            // Handle range estimates (e.g., "65-70 MB")
            if (strpos($this->estimatedBackupSize, '-') !== false) {
                $parts = explode('-', $this->estimatedBackupSize);
                $minSize = floatval(preg_replace('/[^0-9.]/', '', $parts[0]));
                $maxSize = floatval(preg_replace('/[^0-9.]/', '', $parts[1]));
                $avgSize = ($minSize + $maxSize) / 2;
                $totalMB = $avgSize * $maxBackups;

                if ($totalMB >= 1024) {
                    $this->estimatedTotalStorage = round($totalMB / 1024, 1) . ' GB';
                } else {
                    $this->estimatedTotalStorage = round($totalMB, 1) . ' MB';
                }
            } else {
                $this->estimatedTotalStorage = 'Unknown';
            }
        }
    }

    /**
     * Handle max backups change
     */
    public function updatedMaxBackups()
    {
        // Ensure maxBackups is always an integer
        $this->maxBackups = intval($this->maxBackups);
        $this->calculateTotalStorage();
        $this->dispatch('maxBackupsUpdated');
    }

    /**
     * Handle include files change
     */
    public function updatedIncludeFiles()
    {
        // Convert to boolean
        $this->includeFiles = filter_var($this->includeFiles, FILTER_VALIDATE_BOOLEAN);
        $this->updateBackupEstimates();
    }

    public function updatedIncludeModules()
    {
        // Convert to boolean
        $this->includeModules = filter_var($this->includeModules, FILTER_VALIDATE_BOOLEAN);
        $this->updateBackupEstimates();
    }

    public function updatedStorageLocation()
    {
        // If storage_setting is selected, copy configuration from file_storage_settings
        if ($this->storageLocation === 'storage_setting') {
            $this->copyStorageConfigFromFileSettings();
        }
    }

    public function copyStorageConfigFromFileSettings()
    {
        try {
            $fileStorageSetting = \App\Models\StorageSetting::where('status', 'enabled')->first();

            if ($fileStorageSetting) {
                $this->storageConfig = [
                    'filesystem' => $fileStorageSetting->filesystem,
                    'auth_keys' => json_decode($fileStorageSetting->auth_keys, true) ?? [],
                    'status' => $fileStorageSetting->status,
                ];
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Handle any property updates for live calculations
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['maxBackups', 'includeFiles', 'includeModules'])) {
            // Ensure maxBackups is always an integer
            if ($propertyName === 'maxBackups') {
                $this->maxBackups = intval($this->maxBackups);
            }
            $this->updateBackupEstimates();
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;

        // Refresh intelligence data when switching to health tab
        if ($tab === 'health') {
            $this->loadIntelligenceData();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;

        // Refresh intelligence data when switching to health tab
        if ($tab === 'health') {
            $this->loadIntelligenceData();
        }

        // Auto-sync backups when switching to history tab
        if ($tab === 'history') {
            $this->autoSyncBackupsFromStorage();
        }

        // Update URL with both tab=backup and subtab parameters
        $urlParams = request()->query();
        $urlParams['tab'] = 'backup'; // Keep 'backup' as the main tab
        $urlParams['subtab'] = $tab; // Set the subtab

        $newUrl = request()->fullUrlWithQuery($urlParams);

        // Update URL without page reload
        $this->dispatch('url-updated', url: $newUrl);
    }

    /**
     * Automatically sync backups from storage (S3/local) without user notification
     */
    public function autoSyncBackupsFromStorage()
    {
        $this->isSyncingBackups = true;

        try {
            $response = Http::post(route('superadmin.database-backup.sync'));

            if ($response->successful()) {
                $data = $response->json();
                $this->syncResults = $data['results'] ?? [];

                $totalCreated = ($this->syncResults['local']['created'] ?? 0) + ($this->syncResults['cloud']['created'] ?? 0);
                $totalUpdated = ($this->syncResults['local']['updated'] ?? 0) + ($this->syncResults['cloud']['updated'] ?? 0);
                $totalDeleted = $this->syncResults['orphaned']['deleted'] ?? 0;

                // Only show notification if there were actual changes
                if ($totalCreated > 0 || $totalUpdated > 0 || $totalDeleted > 0) {
                    $message = "Auto-sync completed: ";
                    if ($totalCreated > 0) $message .= "$totalCreated new backups found, ";
                    if ($totalUpdated > 0) $message .= "$totalUpdated backups updated, ";
                    if ($totalDeleted > 0) $message .= "$totalDeleted orphaned records removed";

                    $this->alert('info', $message, [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                }

                // Refresh the backup list
                $this->dispatch('refreshBackups');
            }
        } catch (\Exception $e) {
            // Log error but don't show to user for auto-sync
            Log::error('Auto-sync failed: ' . $e->getMessage());
        } finally {
            $this->isSyncingBackups = false;
        }
    }

    public function loadSettings()
    {
        $this->settings = DatabaseBackupSetting::getSettings();
        $this->isEnabled = $this->settings->is_enabled;
        $this->frequency = $this->settings->frequency;
        $this->backupTime = $this->settings->backup_time;
        $this->retentionDays = $this->settings->retention_days;
        $this->maxBackups = $this->settings->max_backups;
        $this->includeFiles = $this->settings->include_files;
        $this->includeModules = $this->settings->include_modules;
        $this->storageLocation = $this->settings->storage_location;
        $this->storageConfig = $this->settings->storage_config ?? [];
    }

    public function loadIntelligenceData()
    {
        $this->recommendations = $this->intelligenceService->getBackupRecommendations();
        $this->dataChangeInsights = $this->intelligenceService->getDataChangeInsights();
        $this->backupHealthScore = $this->intelligenceService->getBackupHealthScore();
    }

    public function refreshBackups()
    {
        // Auto-sync backups from storage first
        $this->autoSyncBackupsFromStorage();

        // This method will be called when the refreshBackups event is dispatched
        $this->dispatch('$refresh');
        $this->loadIntelligenceData();
    }

    public function applyRecommendation($action, $data = null)
    {
        switch ($action) {
            case 'create_backup':
                $this->openCreateBackupModal();
                break;

            case 'change_backup_time':
                if ($data && isset($data['suggested_time'])) {
                    $this->backupTime = $data['suggested_time'];
                    $this->saveSettings();
                    $this->alert('success', 'Backup time updated based on AI recommendation!', [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                }
                break;

            case 'increase_frequency':
                if ($this->frequency === 'weekly') {
                    $this->frequency = 'daily';
                } elseif ($this->frequency === 'monthly') {
                    $this->frequency = 'weekly';
                }
                $this->saveSettings();
                $this->alert('success', 'Backup frequency increased based on growth analysis!', [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
                break;
        }

        $this->loadIntelligenceData();
    }

    public function saveSettings()
    {
        try {
            // Convert string values to proper booleans before validation
            $this->includeModules = filter_var($this->includeModules, FILTER_VALIDATE_BOOLEAN);
            $this->includeFiles = filter_var($this->includeFiles, FILTER_VALIDATE_BOOLEAN);
            $this->isEnabled = filter_var($this->isEnabled, FILTER_VALIDATE_BOOLEAN);

            $this->validate();

            $updateData = [
                'is_enabled' => $this->isEnabled,
                'frequency' => $this->frequency,
                'backup_time' => $this->backupTime,
                'retention_days' => $this->retentionDays,
                'max_backups' => $this->maxBackups,
                'include_files' => $this->includeFiles,
                'include_modules' => $this->includeModules,
                'storage_location' => $this->storageLocation,
                'storage_config' => $this->storageConfig,
            ];

            // Debug: Log the data being saved
            \Log::info('Saving backup settings:', $updateData);

            $result = $this->settings->update($updateData);

            // Debug: Log the result
            \Log::info('Save result:', ['success' => $result]);

            // Refresh intelligence data after saving settings
            $this->loadIntelligenceData();

            $this->alert('success', __('backup::app.databaseBackupSettingsUpdatedSuccessfully'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving backup settings: ' . $e->getMessage());

            $this->alert('error', 'Failed to save settings: ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    public function createManualBackup()
    {
        try {
            $this->isCreatingBackup = true;
            // Keep modal open to show progress
            // $this->showCreateBackupModal = false;

            // Dispatch event to start progress animation
            $this->dispatch('startBackupProgress');

            // Run the backup command
            $result = Artisan::call(CreateDatabaseBackup::class, [
                '--type' => 'manual',
                '--include-files' => $this->includeFiles ? 'true' : 'false',
                '--include-modules' => $this->includeModules ? 'true' : 'false',
            ]);

            $this->alert('success', __('backup::app.databaseBackupCreatedSuccessfully'), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);

            $this->dispatch('refreshBackups');
        } catch (\Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());

            $this->alert('error', __('backup::app.failedToCreateBackup') . ': ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        } finally {
            $this->isCreatingBackup = false;
            $this->showCreateBackupModal = false;
        }
    }



    public function downloadBackup($backupId)
    {
        try {
            $backup = DatabaseBackup::findOrFail($backupId);

            if (!$backup->download_url) {
                $this->alert('error', 'Download URL not available for this backup.', [
                    'toast' => true,
                    'position' => 'top-end',
                ]);
                return;
            }

            // Return the download URL for the browser to handle
            return redirect($backup->download_url);
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to download backup: ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }
    }

    public function confirmDeleteBackup($backupId)
    {
        $this->backupToDelete = DatabaseBackup::findOrFail($backupId);
        $this->showDeleteBackupModal = true;
    }

    public function deleteBackup()
    {
        if ($this->backupToDelete) {
            try {
                // Delete the file if it exists
                if (file_exists($this->backupToDelete->file_path)) {
                    unlink($this->backupToDelete->file_path);
                }

                $this->backupToDelete->delete();

                $this->alert('success', __('backup::app.backupDeletedSuccessfully'), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);

                $this->dispatch('refreshBackups');
            } catch (\Exception $e) {
                $this->alert('error', __('backup::app.failedToDeleteBackup') . $e->getMessage(), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }
        }

        $this->showDeleteBackupModal = false;
        $this->backupToDelete = null;
    }

    public function getFrequencyOptions()
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }

    public function getStorageLocationOptions()
    {
        // Get the current enabled storage setting
        $currentStorage = \App\Models\StorageSetting::where('status', 'enabled')->first();

        $options = [
            'local' => 'Local Storage',
        ];

        // Add the current storage setting if it's not local
        if ($currentStorage && $currentStorage->filesystem !== 'local') {
            $storageName = match ($currentStorage->filesystem) {
                'aws_s3' => 'Amazon S3',
                'digitalocean' => 'DigitalOcean Spaces',
                'wasabi' => 'Wasabi',
                'minio' => 'MinIO',
                default => ucfirst($currentStorage->filesystem),
            };

            $options['storage_setting'] = $storageName . ' (Current Setting)';
        }

        return $options;
    }

    public function getNextBackupTime()
    {
        return $this->settings->next_backup_time;
    }

    public function getCronExpression()
    {
        if (!$this->isEnabled) {
            return null;
        }

        $timeParts = explode(':', $this->backupTime);
        $minute = (int) $timeParts[1];
        $hour = (int) $timeParts[0];

        switch ($this->frequency) {
            case 'daily':
                return "{$minute} {$hour} * * *";
            case 'weekly':
                return "{$minute} {$hour} * * 0"; // Sunday
            case 'monthly':
                return "{$minute} {$hour} 1 * *"; // 1st of month
            default:
                return "{$minute} {$hour} * * *";
        }
    }

    /**
     * Run backup health check
     */
    public function runBackupHealthCheck()
    {
        $this->isRunningHealthCheck = true;
        $this->healthIssues = [];
        $this->healthCheckCompleted = false;

        try {
            $response = Http::get(route('superadmin.database-backup.health'));

            if ($response->successful()) {
                $data = $response->json();
                $this->healthIssues = $data['issues'] ?? [];
                $this->healthCheckCompleted = true;

                if (empty($this->healthIssues)) {
                    $this->alert('success', 'Backup health check passed! All backups are properly synchronized.', [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                } else {
                    $this->alert('warning', 'Backup health check found ' . count($this->healthIssues) . ' issues.', [
                        'toast' => true,
                        'position' => 'top-end',
                        'showCancelButton' => false,
                        'cancelButtonText' => __('app.close')
                    ]);
                }
            } else {
                $this->alert('error', 'Failed to run health check: ' . $response->body(), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to run health check: ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }

        $this->isRunningHealthCheck = false;
    }

    /**
     * Sync backups from storage
     */
    public function syncBackupsWithStorage()
    {
        try {
            $response = Http::post(route('superadmin.database-backup.sync'));

            if ($response->successful()) {
                $data = $response->json();
                $this->syncResults = $data['results'] ?? [];

                $totalCreated = ($this->syncResults['local']['created'] ?? 0) + ($this->syncResults['cloud']['created'] ?? 0);
                $totalUpdated = ($this->syncResults['local']['updated'] ?? 0) + ($this->syncResults['cloud']['updated'] ?? 0);
                $totalDeleted = $this->syncResults['orphaned']['deleted'] ?? 0;

                $message = "Sync completed: ";
                if ($totalCreated > 0) $message .= "$totalCreated created, ";
                if ($totalUpdated > 0) $message .= "$totalUpdated updated, ";
                if ($totalDeleted > 0) $message .= "$totalDeleted orphaned records deleted";

                $this->alert('success', $message, [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);

                // Refresh the backup list
                $this->dispatch('refreshBackups');
            } else {
                $this->alert('error', 'Failed to sync backups: ' . $response->body(), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to sync backups: ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    /**
     * Clean up orphaned backup records
     */
    public function cleanupOrphanedBackups()
    {
        try {
            $response = Http::post(route('superadmin.database-backup.sync'));

            if ($response->successful()) {
                $data = $response->json();
                $deleted = $data['results']['orphaned']['deleted'] ?? 0;

                $this->alert('success', "Cleaned up $deleted orphaned backup records.", [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);

                // Refresh the backup list
                $this->dispatch('refreshBackups');
            } else {
                $this->alert('error', 'Failed to cleanup orphaned backups: ' . $response->body(), [
                    'toast' => true,
                    'position' => 'top-end',
                    'showCancelButton' => false,
                    'cancelButtonText' => __('app.close')
                ]);
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to cleanup orphaned backups: ' . $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
                'showCancelButton' => false,
                'cancelButtonText' => __('app.close')
            ]);
        }
    }

    public function render()
    {
        $backups = DatabaseBackup::orderBy('created_at', 'desc')
            ->paginate(5);

        return view('backup::livewire.superadmin-settings.database-backup-settings', [
            'backups' => $backups,
            'settings' => $this->settings,
            'frequencyOptions' => $this->getFrequencyOptions(),
            'storageLocationOptions' => $this->getStorageLocationOptions(),
            'nextBackupTime' => $this->getNextBackupTime(),
            'cronExpression' => $this->getCronExpression(),
            'recommendations' => $this->recommendations,
            'dataChangeInsights' => $this->dataChangeInsights,
            'backupHealthScore' => $this->backupHealthScore,
            'estimatedBackupSize' => $this->estimatedBackupSize,
            'estimatedTotalStorage' => $this->estimatedTotalStorage,
            'currentTotalBackupSize' => $this->currentTotalBackupSize,
            'healthIssues' => $this->healthIssues,
            'healthCheckCompleted' => $this->healthCheckCompleted,
            'isRunningHealthCheck' => $this->isRunningHealthCheck,
            'syncResults' => $this->syncResults,
            'isSyncingBackups' => $this->isSyncingBackups,
        ]);
    }
}
