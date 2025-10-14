<?php

use Illuminate\Support\Facades\Route;
use Modules\Backup\Http\Controllers\DatabaseBackupController;
use App\Http\Middleware\SuperAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
    SuperAdmin::class,
])->group(function () {

    Route::name('superadmin.')->group(function () {
        // Database Backup Routes
        Route::get('database-backup/{backup}/download', [DatabaseBackupController::class, 'download'])->name('database-backup.download');
        Route::delete('database-backup/{backup}', [DatabaseBackupController::class, 'destroy'])->name('database-backup.destroy');
        Route::get('database-backup/statistics', [DatabaseBackupController::class, 'statistics'])->name('database-backup.statistics');

        // New backup sync and health check routes
        Route::post('database-backup/sync', [DatabaseBackupController::class, 'syncBackupsFromStorage'])->name('database-backup.sync');
        Route::get('database-backup/health', [DatabaseBackupController::class, 'healthCheck'])->name('database-backup.health');
    });
});

// Update backup route (no middleware for update process)
Route::post('admin/update-version/createBackup', [\Modules\Backup\Http\Controllers\UpdateBackupController::class, 'createBackup'])->name('admin.updateVersion.createBackup');
