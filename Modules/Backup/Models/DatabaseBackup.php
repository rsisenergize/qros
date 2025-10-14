<?php

namespace Modules\Backup\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DatabaseBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'status',
        'error_message',
        'backup_type',
        'version',
        'stored_on',
        'created_at',
        'updated_at',
        'completed_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'completed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Completed</span>',
            'failed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Failed</span>',
            'in_progress' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">In Progress</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">Unknown</span>',
        };
    }

    public function getTypeBadgeAttribute()
    {
        return match ($this->backup_type) {
            'manual' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Manual</span>',
            'scheduled' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">Scheduled</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">Unknown</span>',
        };
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->status !== 'completed') {
            return null;
        }

        // Check if it's a cloud storage path
        if (str_starts_with($this->file_path, 'backups/')) {
            // Cloud storage path - always return download URL
            return route('superadmin.database-backup.download', $this->id);
        }

        // Local file path - check if file exists
        if (file_exists($this->file_path)) {
            return route('superadmin.database-backup.download', $this->id);
        }

        return null;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Check if this backup includes files
     */
    public function getIncludesFilesAttribute()
    {
        return str_contains($this->filename, 'combined_backup') || str_contains($this->filename, 'files_backup');
    }

    /**
     * Get backup type with file information
     */
    public function getBackupTypeWithFilesAttribute()
    {
        $type = $this->backup_type === 'manual' ? 'Manual' : 'Scheduled';
        return $this->includes_files ? $type . ' (with files)' : $type;
    }

    /**
     * Get version badge
     */
    public function getVersionBadgeAttribute()
    {
        if (!$this->version) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">Unknown</span>';
        }

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">v' . $this->version . '</span>';
    }

    /**
     * Get total backup size for all completed backups
     */
    public static function getTotalBackupSize()
    {
        $totalSize = self::completed()->sum('file_size');

        if ($totalSize <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $totalSize;

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get estimated backup size based on settings
     */
    public static function getEstimatedBackupSize()
    {
        $settings = \Modules\Backup\Models\DatabaseBackupSetting::getSettings();

        // Get average size of recent backups
        $recentBackups = self::completed()->orderBy('created_at', 'desc')->limit(5)->get();

        if ($recentBackups->isEmpty()) {
            // Default estimates
            $estimatedSize = $settings->include_files ? '50-100 MB' : '5-10 MB';
        } else {
            $avgSize = $recentBackups->avg('file_size');
            $avgSizeMB = round($avgSize / 1024 / 1024, 1);
            $estimatedSize = $avgSizeMB . ' MB';
        }

        return $estimatedSize;
    }

    /**
     * Get estimated total storage based on max backups setting
     */
    public static function getEstimatedTotalStorage()
    {
        $settings = \Modules\Backup\Models\DatabaseBackupSetting::getSettings();
        $maxBackups = $settings->max_backups;

        $estimatedSize = self::getEstimatedBackupSize();

        // Extract number from estimated size
        if (preg_match('/(\d+(?:\.\d+)?)/', $estimatedSize, $matches)) {
            $sizeMB = floatval($matches[1]);
            $totalMB = $sizeMB * $maxBackups;

            if ($totalMB >= 1024) {
                return round($totalMB / 1024, 1) . ' GB';
            } else {
                return round($totalMB, 1) . ' MB';
            }
        }

        return 'Unknown';
    }

    /**
     * Get storage display name
     */
    public function getStorageDisplayNameAttribute()
    {
        switch ($this->stored_on) {
            case 'local':
                return 'Local Storage';
            case 'aws_s3':
                return 'AWS S3';
            case 'wasabi':
                return 'wasabi';
            case 'digitalocean':
                return 'DigitalOcean Spaces';
            case 'minio':
                return 'MinIO';
            default:
                return ucfirst(str_replace('_', ' ', $this->stored_on));
        }
    }

    /**
     * Get storage badge color
     */
    public function getStorageBadgeAttribute()
    {
        switch ($this->stored_on) {
            case 'local':
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
            case 'aws_s3':
                return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
            case 'wasabi':
                return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
            case 'digitalocean':
                return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
            case 'minio':
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        }
    }
}
