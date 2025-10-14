<?php

namespace Modules\Backup\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseBackupSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'frequency',
        'backup_time',
        'retention_days',
        'max_backups',
        'include_files',
        'include_modules',
        'storage_location',
        'storage_config',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'include_files' => 'boolean',
        'include_modules' => 'boolean',
        'storage_config' => 'array',
    ];

    public static function getSettings()
    {
        return static::firstOrCreate([], [
            'is_enabled' => false,
            'frequency' => 'daily',
            'backup_time' => '02:00:00',
            'retention_days' => 30,
            'max_backups' => 10,
            'include_files' => false,
            'include_modules' => false,
            'storage_location' => 'local',
        ]);
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
        return [
            'local' => 'Local Storage',
            'storage_setting' => 'Storage Setting',
        ];
    }

    public function getNextBackupTimeAttribute()
    {
        if (!$this->is_enabled) {
            return null;
        }

        $now = now();
        $backupTime = $this->backup_time;
        $nextBackup = $now->copy()->setTimeFromTimeString($backupTime);

        // If today's backup time has passed, move to next occurrence
        if ($nextBackup->isPast()) {
            switch ($this->frequency) {
                case 'daily':
                    $nextBackup->addDay();
                    break;
                case 'weekly':
                    $nextBackup->addWeek();
                    break;
                case 'monthly':
                    $nextBackup->addMonth();
                    break;
            }
        }

        return $nextBackup;
    }

    public function getFormattedBackupTimeAttribute()
    {
        return $this->backup_time ? date('g:i A', strtotime($this->backup_time)) : 'Not set';
    }

    public function getFormattedFrequencyAttribute()
    {
        return ucfirst($this->frequency ?? 'daily');
    }

    public function getFormattedStorageLocationAttribute()
    {
        return $this->storage_location === 'local' ? 'Local Storage' : 'Cloud Storage';
    }

    /**
     * Get estimated backup size
     */
    public function getEstimatedBackupSizeAttribute()
    {
        return \Modules\Backup\Models\DatabaseBackup::getEstimatedBackupSize();
    }

    /**
     * Get estimated total storage based on max backups
     */
    public function getEstimatedTotalStorageAttribute()
    {
        return \Modules\Backup\Models\DatabaseBackup::getEstimatedTotalStorage();
    }

    /**
     * Get total backup size for all completed backups
     */
    public function getTotalBackupSizeAttribute()
    {
        return \Modules\Backup\Models\DatabaseBackup::getTotalBackupSize();
    }

    public function getCronExpressionAttribute()
    {
        if (!$this->is_enabled) {
            return null;
        }

        $timeParts = explode(':', $this->backup_time);
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
}
