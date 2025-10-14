<?php

namespace App\Observers;

use App\Models\TableSession;
use Illuminate\Support\Facades\Cache;

class TableSessionObserver
{
    /**
     * Handle the TableSession "created" event.
     */
    public function creating(TableSession $tableSession): void
    {
        if (branch()) {
            $tableSession->branch_id = branch()->id;
        }
    }

    /**
     * Handle the TableSession "updated" event.
     */
    public function updated(TableSession $tableSession): void
    {
        // Only auto-cleanup expired sessions occasionally to avoid performance issues
        if (rand(1, 10) === 1) { // 10% chance to run cleanup
            $this->cleanupExpiredSessions();
        }

        // Update cache
        if ($tableSession->isLocked()) {
            Cache::put(
                "table_session_{$tableSession->table_id}",
                $tableSession->id,
                now()->addMinutes(10)
            );
        } else {
            Cache::forget("table_session_{$tableSession->table_id}");
        }
    }

    /**
     * Handle the TableSession "deleting" event.
     */
    public function deleting(TableSession $tableSession): void
    {
        Cache::forget("table_session_{$tableSession->table_id}");
    }

    /**
     * Auto-cleanup expired sessions using centralized method
     */
    private function cleanupExpiredSessions(): void
    {
        \App\Models\Table::cleanupExpiredLocks();
    }
}
