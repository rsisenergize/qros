<?php

namespace Modules\Backup\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Backup\Models\DatabaseBackup;
use App\Http\Controllers\Controller;
use App\Models\StorageSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseBackupController extends Controller
{
    /**
     * Download a database backup file
     */
    public function download(DatabaseBackup $backup)
    {
        // Check if user has permission to download backups
        if (!user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if backup is completed
        if ($backup->status !== 'completed') {
            abort(400, 'Backup is not ready for download.');
        }

        $fileName = $backup->filename;
        $filePath = $backup->file_path;

        // Check if it's a cloud storage path
        if (str_starts_with($filePath, 'backups/')) {
            // Cloud storage path
            try {
                // Get the current storage setting
                $storageSetting = StorageSetting::where('status', 'enabled')->first();

                if (!$storageSetting || $storageSetting->filesystem === 'local') {
                    abort(404, 'Cloud storage not configured.');
                }

                // Check if file exists in cloud storage
                if (!Storage::disk($storageSetting->filesystem)->exists($filePath)) {
                    abort(404, 'Backup file not found in cloud storage.');
                }

                // Return streamed response for cloud storage
                $stream = Storage::disk($storageSetting->filesystem)->readStream($filePath);

                return response()->stream(function () use ($stream) {
                    fpassthru($stream);
                }, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            } catch (\Exception $e) {
                abort(500, 'Failed to download from cloud storage: ' . $e->getMessage());
            }
        } else {
            // Local file path
            if (!file_exists($filePath)) {
                abort(404, 'Backup file not found.');
            }

            // Return file download response for local files
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }
    }

    /**
     * Delete a database backup
     */
    public function destroy(DatabaseBackup $backup)
    {
        // Check if user has permission to delete backups
        if (!user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $filePath = $backup->file_path;

            // Check if it's a cloud storage path
            if (str_starts_with($filePath, 'backups/')) {
                // Delete from cloud storage
                $storageSetting = StorageSetting::where('status', 'enabled')->first();

                if ($storageSetting && $storageSetting->filesystem !== 'local') {
                    if (Storage::disk($storageSetting->filesystem)->exists($filePath)) {
                        Storage::disk($storageSetting->filesystem)->delete($filePath);
                    }
                }
            } else {
                // Delete local file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $backup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync backup files from storage with database
     */
    public function syncBackupsFromStorage()
    {
        // Check if user has permission
        if (!user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $results = [
                'local' => $this->syncLocalBackups(),
                'cloud' => $this->syncCloudBackups(),
                'orphaned' => $this->cleanupOrphanedRecords(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Backup synchronization completed.',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync backups: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync local backup files
     */
    private function syncLocalBackups()
    {
        $localBackupDir = storage_path('app/backups');
        $results = [
            'found' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => []
        ];

        if (!is_dir($localBackupDir)) {
            return $results;
        }

        $files = glob($localBackupDir . '/*.{zip,tar.gz,sql,sql.gz}', GLOB_BRACE);

        foreach ($files as $file) {
            try {
                $filename = basename($file);
                $fileSize = filesize($file);
                $fileModified = filemtime($file);

                // Parse backup info from filename
                $backupInfo = $this->parseBackupFilename($filename);

                if (!$backupInfo) {
                    $results['errors'][] = "Could not parse filename: $filename";
                    continue;
                }

                $results['found']++;

                // Extract timestamp from filename
                $timestamp = $this->parseTimestampFromFilename($filename);
                $createdAt = $timestamp ? Carbon::createFromFormat('Y-m-d_H-i-s', $timestamp) : Carbon::createFromTimestamp($fileModified);

                // Check if backup exists in database
                $existingBackup = DatabaseBackup::where('filename', $filename)->first();

                if (!$existingBackup) {
                    // Create new database record
                    $backup = new DatabaseBackup([
                        'filename' => $filename,
                        'file_path' => $file,
                        'file_size' => $fileSize,
                        'type' => $backupInfo['type'],
                        'status' => 'completed',
                        'version' => $backupInfo['version'] ?? 'unknown',
                        'stored_on' => 'local',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'completed_at' => $createdAt,
                    ]);
                    $backup->timestamps = false; // Disable automatic timestamps
                    $backup->save();
                    $results['created']++;
                } else {
                    // Update existing record if file size or modification time changed
                    if (
                        $existingBackup->file_size != $fileSize ||
                        $existingBackup->updated_at->timestamp != $fileModified
                    ) {
                        $existingBackup->timestamps = false; // Disable automatic timestamps
                        $existingBackup->update([
                            'file_size' => $fileSize,
                            'stored_on' => 'local',
                            'updated_at' => $createdAt,
                            'completed_at' => $createdAt,
                        ]);
                        $results['updated']++;
                    }
                }
            } catch (\Exception $e) {
                $results['errors'][] = "Error processing $filename: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Sync cloud backup files
     */
    private function syncCloudBackups()
    {
        $results = [
            'found' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => []
        ];

        try {
            $storageSetting = StorageSetting::where('status', 'enabled')
                ->where('filesystem', '!=', 'local')
                ->first();

            if (!$storageSetting) {
                return $results;
            }

            $disk = Storage::disk($storageSetting->filesystem);
            $files = $disk->files('backups');

            foreach ($files as $filePath) {
                try {
                    $filename = basename($filePath);

                    // Skip if not a backup file
                    if (!preg_match('/\.(zip|tar\.gz|sql|sql\.gz)$/', $filename)) {
                        continue;
                    }

                    $fileSize = $disk->size($filePath);
                    $fileModified = $disk->lastModified($filePath);

                    // Parse backup info from filename
                    $backupInfo = $this->parseBackupFilename($filename);

                    if (!$backupInfo) {
                        $results['errors'][] = "Could not parse filename: $filename";
                        continue;
                    }

                    $results['found']++;

                    // Extract timestamp from filename
                    $timestamp = $this->parseTimestampFromFilename($filename);
                    $createdAt = $timestamp ? Carbon::createFromFormat('Y-m-d_H-i-s', $timestamp) : Carbon::createFromTimestamp($fileModified);

                    // Check if backup exists in database
                    $existingBackup = DatabaseBackup::where('filename', $filename)->first();

                    if (!$existingBackup) {
                        // Create new database record
                        $backup = new DatabaseBackup([
                            'filename' => $filename,
                            'file_path' => $filePath,
                            'file_size' => $fileSize,
                            'type' => $backupInfo['type'],
                            'status' => 'completed',
                            'version' => $backupInfo['version'] ?? 'unknown',
                            'stored_on' => $storageSetting->filesystem, // Use the actual filesystem driver
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                            'completed_at' => $createdAt,
                        ]);
                        $backup->timestamps = false; // Disable automatic timestamps
                        $backup->save();
                        $results['created']++;
                    } else {
                        // Update existing record if file size or modification time changed
                        if (
                            $existingBackup->file_size != $fileSize ||
                            $existingBackup->updated_at->timestamp != $fileModified
                        ) {
                            $existingBackup->timestamps = false; // Disable automatic timestamps
                            $existingBackup->update([
                                'file_size' => $fileSize,
                                'stored_on' => $storageSetting->filesystem, // Use the actual filesystem driver
                                'updated_at' => $createdAt,
                                'completed_at' => $createdAt,
                            ]);
                            $results['updated']++;
                        }
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = "Error processing $filename: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $results['errors'][] = "Cloud storage error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Clean up orphaned database records (files that don't exist)
     */
    private function cleanupOrphanedRecords()
    {
        $results = [
            'checked' => 0,
            'deleted' => 0,
            'errors' => []
        ];

        $backups = DatabaseBackup::all();

        foreach ($backups as $backup) {
            try {
                $results['checked']++;
                $fileExists = false;

                if (str_starts_with($backup->file_path, 'backups/')) {
                    // Cloud storage file
                    $storageSetting = StorageSetting::where('status', 'enabled')
                        ->where('filesystem', '!=', 'local')
                        ->first();

                    if ($storageSetting) {
                        $fileExists = Storage::disk($storageSetting->filesystem)->exists($backup->file_path);
                    }
                } else {
                    // Local file
                    $fileExists = file_exists($backup->file_path);
                }

                if (!$fileExists) {
                    $backup->delete();
                    $results['deleted']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = "Error checking backup {$backup->filename}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Parse backup filename to extract information
     */
    private function parseBackupFilename($filename)
    {
        // Patterns for different backup types
        $patterns = [
            // combined_backup_v1.2.38_2025-08-04_05-38-11.tar.gz
            'combined' => '/^combined_backup_v([\d.]+)_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.tar\.gz$/',
            // combined_backup_v1.2.38_2025-08-04_06-49-19.zip
            'combined_zip' => '/^combined_backup_v([\d.]+)_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.zip$/',
            // database_backup_2025-08-04_05-38-11.sql
            'database' => '/^database_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql$/',
            // database_backup_2025-08-04_05-38-11.sql.gz
            'database_gz' => '/^database_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql\.gz$/',
            // files_backup_v1.2.38_2025-08-04_05-37-50.tar.gz
            'files' => '/^files_backup_v([\d.]+)_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.tar\.gz$/',
            // files_backup_v1.2.38_2025-08-04_05-37-50.zip
            'files_zip' => '/^files_backup_v([\d.]+)_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.zip$/',
        ];

        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $filename, $matches)) {
                $info = ['type' => $type];

                if ($type === 'combined' || $type === 'combined_zip' || $type === 'files' || $type === 'files_zip') {
                    $info['version'] = $matches[1];
                    $info['date'] = $matches[2];
                } else {
                    $info['date'] = $matches[1];
                }

                return $info;
            }
        }

        return null;
    }

    /**
     * Parse timestamp from backup filename
     */
    private function parseTimestampFromFilename($filename)
    {
        // Extract timestamp from various backup filename patterns
        $patterns = [
            // combined_backup_v1.2.38_2025-08-04_06-49-19.zip
            '/^combined_backup_v[\d.]+_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.(zip|tar\.gz)$/',
            // database_backup_2025-08-04_06-49-19.sql
            '/^database_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql(\.gz)?$/',
            // files_backup_v1.2.38_2025-08-04_06-49-19.zip
            '/^files_backup_v[\d.]+_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.(zip|tar\.gz)$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $filename, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Get backup statistics
     */
    public function statistics()
    {
        // Check if user has permission
        if (!user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $totalBackups = DatabaseBackup::count();
        $completedBackups = DatabaseBackup::completed()->count();
        $failedBackups = DatabaseBackup::failed()->count();
        $recentBackups = DatabaseBackup::recent(7)->count();

        $totalSize = DatabaseBackup::completed()->sum('file_size');
        $averageSize = $completedBackups > 0 ? $totalSize / $completedBackups : 0;

        return response()->json([
            'total_backups' => $totalBackups,
            'completed_backups' => $completedBackups,
            'failed_backups' => $failedBackups,
            'recent_backups' => $recentBackups,
            'total_size' => $this->formatBytes($totalSize),
            'average_size' => $this->formatBytes($averageSize),
        ]);
    }

    /**
     * Get backup health status
     */
    public function healthCheck()
    {
        // Check if user has permission
        if (!user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $issues = [];
        $backups = DatabaseBackup::all();

        foreach ($backups as $backup) {
            $fileExists = false;
            $fileSize = 0;

            try {
                if (str_starts_with($backup->file_path, 'backups/')) {
                    // Cloud storage file
                    $storageSetting = StorageSetting::where('status', 'enabled')
                        ->where('filesystem', '!=', 'local')
                        ->first();

                    if ($storageSetting) {
                        $fileExists = Storage::disk($storageSetting->filesystem)->exists($backup->file_path);
                        if ($fileExists) {
                            $fileSize = Storage::disk($storageSetting->filesystem)->size($backup->file_path);
                        }
                    }
                } else {
                    // Local file
                    $fileExists = file_exists($backup->file_path);
                    if ($fileExists) {
                        $fileSize = filesize($backup->file_path);
                    }
                }

                if (!$fileExists) {
                    $issues[] = "Backup file missing: {$backup->filename}";
                } elseif ($fileSize != $backup->file_size) {
                    $issues[] = "File size mismatch for {$backup->filename}: DB={$backup->file_size}, File={$fileSize}";
                }
            } catch (\Exception $e) {
                $issues[] = "Error checking {$backup->filename}: " . $e->getMessage();
            }
        }

        return response()->json([
            'healthy' => empty($issues),
            'issues' => $issues,
            'total_backups' => $backups->count(),
            'issues_count' => count($issues)
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
