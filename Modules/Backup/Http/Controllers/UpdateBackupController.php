<?php

namespace Modules\Backup\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class UpdateBackupController extends Controller
{
    public function createBackup()
    {
        try {
            // Check if backup module is enabled
            if (!function_exists('module_enabled') || !module_enabled('Backup')) {
                return Reply::error('Backup module is not enabled or not available.');
            }

            // Check if the backup command exists
            if (!class_exists('Modules\Backup\Console\Commands\CreateDatabaseBackup')) {
                return Reply::error('Backup command not found.');
            }

            // Create a backup using the existing command
            $command = 'backup:database';
            $params = [
                '--include-files' => 'true',
                '--include-modules' => 'false',
                '--type' => 'manual'
            ];

            $exitCode = Artisan::call($command, $params);

            if ($exitCode === 0) {
                return Reply::success('Backup created successfully before update.');
            } else {
                return Reply::error('Failed to create backup. Please try again.');
            }
        } catch (\Exception $e) {
            return Reply::error('Error creating backup: ' . $e->getMessage());
        }
    }
}
