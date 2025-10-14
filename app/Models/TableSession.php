<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TableSession extends BaseModel
{
    use HasBranch;
    protected $guarded = ['id'];

    protected $casts = [
        'locked_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->session_token) {
                $model->session_token = Str::random(32);
            }
        });
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function lockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }

    /**
     * Check if the session is locked
     */
    public function isLocked(): bool
    {
        return !is_null($this->locked_by_user_id) && !is_null($this->locked_at);
    }

    /**
     * Check if the session is locked by a specific user
     */
    public function isLockedByUser(int $userId): bool
    {
        return $this->locked_by_user_id === $userId;
    }

    /**
     * Check if the lock has expired
     */
    public function isLockExpired(int $lockTimeoutMinutes = 5): bool
    {
        if (!$this->isLocked() || !$this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->addMinutes($lockTimeoutMinutes)->isPast();
    }

    /**
     * Lock the session for a user
     */
    public function lockForUser(int $userId): bool
    {
        return $this->update([
            'locked_by_user_id' => $userId,
            'locked_at' => now(),
            'last_activity_at' => now(),
            'session_token' => Str::random(32),
        ]);
    }

    /**
     * Update the last activity timestamp
     */
    public function updateActivity(): bool
    {
        if (!$this->isLocked()) {
            return false;
        }

        return $this->update([
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Release the lock
     */
    public function releaseLock(): bool
    {
        return $this->update([
            'locked_by_user_id' => null,
            'locked_at' => null,
            'last_activity_at' => null,
            'session_token' => null,
        ]);
    }

    /**
     * Check if a user can access this session
     */
    public function canBeAccessedByUser(int $userId, int $lockTimeoutMinutes = 5): bool
    {
        // Session is not locked
        if (!$this->isLocked()) {
            return true;
        }

        // Session is locked by the same user
        if ($this->isLockedByUser($userId)) {
            return true;
        }

        // Session lock has expired
        if ($this->isLockExpired($lockTimeoutMinutes)) {
            return true;
        }

        return false;
    }
}
