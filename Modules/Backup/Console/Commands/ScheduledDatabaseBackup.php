<?php

namespace Modules\Backup\Console\Commands;


use Illuminate\Console\Command;
use Modules\Backup\Models\DatabaseBackupSetting;

class ScheduledDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database:scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a scheduled database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = DatabaseBackupSetting::getSettings();

        if (!$settings->is_enabled) {
            $this->info('Scheduled backups are disabled.');
            return Command::SUCCESS;
        }

        $this->info('Creating scheduled database backup...');

        // Call the main backup command with scheduled type
        $this->call('backup:database', [
            '--type' => 'scheduled',
        ]);

        return Command::SUCCESS;
    }
}
