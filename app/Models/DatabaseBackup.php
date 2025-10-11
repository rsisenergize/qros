<?php

namespace App\Models;

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
        'completed_at',
    ];

    protected $casts = [
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
        if ($this->includes_files) {
            $type .= ' (with files)';
        }
        return $type;
    }
}
