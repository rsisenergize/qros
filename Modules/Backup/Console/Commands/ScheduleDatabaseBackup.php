<?php

namespace Modules\Backup\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Backup\Models\DatabaseBackupSetting;

class ScheduleDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database:schedule-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if it\'s time to create a scheduled database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = DatabaseBackupSetting::getSettings();

        if (!$settings->is_enabled) {
            return Command::SUCCESS;
        }

        $now = now();
        $backupTime = $settings->backup_time;

        // Parse the backup time
        $backupHour = (int) substr($backupTime, 0, 2);
        $backupMinute = (int) substr($backupTime, 3, 2);

        // Check if it's time to run the backup
        if ($now->hour === $backupHour && $now->minute === $backupMinute) {
            Log::info('Scheduled backup time reached. Creating backup...');

            // Call the main backup command with scheduled type
            $this->call('backup:database', [
                '--type' => 'scheduled',
            ]);
        }

        return Command::SUCCESS;
    }
}
