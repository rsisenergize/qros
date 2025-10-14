<?php

namespace Modules\Backup\Services;

use Modules\Backup\Models\DatabaseBackup;
use Modules\Backup\Models\DatabaseBackupSetting;
use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\FileStorage;


class BackupIntelligenceService
{
    /**
     * Get AI-powered backup recommendations
     */
    public function getBackupRecommendations()
    {
        $recommendations = [];

        // Check for missed backups
        $missedBackupWarning = $this->checkMissedBackups();
        if ($missedBackupWarning) {
            $recommendations[] = $missedBackupWarning;
        }

        // Get optimal backup time recommendation
        $optimalTimeRecommendation = $this->getOptimalBackupTime();
        if ($optimalTimeRecommendation) {
            $recommendations[] = $optimalTimeRecommendation;
        }

        // Get traffic-based recommendations
        $trafficRecommendation = $this->getTrafficBasedRecommendation();
        if ($trafficRecommendation) {
            $recommendations[] = $trafficRecommendation;
        }

        // Get file storage-based recommendations
        $fileStorageRecommendation = $this->getFileStorageRecommendation();
        if ($fileStorageRecommendation) {
            $recommendations[] = $fileStorageRecommendation;
        }

        return $recommendations;
    }

    /**
     * Check for missed backups and warn users
     */
    private function checkMissedBackups()
    {
        $settings = DatabaseBackupSetting::getSettings();

        if (!$settings->is_enabled) {
            return null;
        }

        $lastBackup = DatabaseBackup::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastBackup) {
            return [
                'type' => 'warning',
                'title' => 'No Backups Found',
                'message' => 'No database backups have been created yet. Consider creating your first backup.',
                'icon' => 'exclamation-triangle',
                'action' => 'create_backup'
            ];
        }

        $daysSinceLastBackup = $lastBackup->created_at->diffInDays(now());

        // Warn if no backup in 7 days
        if ($daysSinceLastBackup >= 7) {
            return [
                'type' => 'warning',
                'title' => 'Backup Overdue',
                'message' => "No backup has been taken in {$daysSinceLastBackup} days. Consider creating a backup soon.",
                'icon' => 'clock',
                'action' => 'create_backup',
                'days_since' => $daysSinceLastBackup
            ];
        }

        // Warn if no backup in 3 days
        if ($daysSinceLastBackup >= 3) {
            return [
                'type' => 'info',
                'title' => 'Backup Due Soon',
                'message' => "It's been {$daysSinceLastBackup} days since the last backup. Consider scheduling one soon.",
                'icon' => 'calendar',
                'action' => 'schedule_backup',
                'days_since' => $daysSinceLastBackup
            ];
        }

        return null;
    }

    /**
     * Get optimal backup time based on traffic patterns
     */
    private function getOptimalBackupTime()
    {
        // Analyze order patterns for the last 30 days
        $orderPatterns = $this->analyzeOrderPatterns();

        if (empty($orderPatterns)) {
            return null;
        }

        // Find the hour with the lowest order activity
        $lowestActivityHour = $orderPatterns['lowest_activity_hour'];
        $lowestActivityCount = $orderPatterns['lowest_activity_count'];
        $highestActivityHour = $orderPatterns['highest_activity_hour'];
        $highestActivityCount = $orderPatterns['highest_activity_count'];

        $currentSettings = DatabaseBackupSetting::getSettings();
        $currentBackupHour = (int) substr($currentSettings->backup_time, 0, 2);

        // If current backup time is during high activity, suggest a better time
        if (
            $currentBackupHour === $highestActivityHour ||
            ($currentBackupHour >= $highestActivityHour - 1 && $currentBackupHour <= $highestActivityHour + 1)
        ) {

            return [
                'type' => 'recommendation',
                'title' => 'Optimal Backup Time',
                'message' => "Your current backup time ({$currentBackupHour}:00) coincides with peak order activity ({$highestActivityCount} orders/hour at {$highestActivityHour}:00). Consider changing to {$lowestActivityHour}:00 for minimal impact.",
                'icon' => 'clock',
                'action' => 'change_backup_time',
                'suggested_time' => sprintf('%02d:00:00', $lowestActivityHour),
                'current_activity' => $highestActivityCount,
                'suggested_activity' => $lowestActivityCount
            ];
        }

        return null;
    }

    /**
     * Get traffic-based recommendations
     */
    private function getTrafficBasedRecommendation()
    {
        $recentOrders = Order::where('created_at', '>=', now()->subDays(7))->count();
        $previousWeekOrders = Order::whereBetween('created_at', [
            now()->subDays(14),
            now()->subDays(7)
        ])->count();

        $growth = $recentOrders - $previousWeekOrders;
        $growthPercentage = $previousWeekOrders > 0 ? ($growth / $previousWeekOrders) * 100 : 0;

        if ($growthPercentage > 50) {
            return [
                'type' => 'info',
                'title' => 'High Growth Detected',
                'message' => "Order volume has increased by " . round($growthPercentage, 1) . "% in the last week. Consider increasing backup frequency.",
                'icon' => 'trending-up',
                'action' => 'increase_frequency',
                'growth_percentage' => $growthPercentage,
                'recent_orders' => $recentOrders
            ];
        }

        return null;
    }

    /**
     * Get file storage-based recommendations
     */
    private function getFileStorageRecommendation()
    {
        $lastBackup = DatabaseBackup::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastBackup) {
            return null;
        }

        $sinceLastBackup = $lastBackup->created_at;

        // Get files created since last backup
        $recentFiles = FileStorage::where('created_at', '>', $sinceLastBackup)->count();
        $recentFileSize = FileStorage::where('created_at', '>', $sinceLastBackup)->sum('size');

        // Get files from the week before the backup for comparison
        $weekBeforeBackup = $sinceLastBackup->copy()->subDays(7);
        $previousWeekFiles = FileStorage::whereBetween('created_at', [
            $weekBeforeBackup,
            $sinceLastBackup
        ])->count();
        $previousWeekFileSize = FileStorage::whereBetween('created_at', [
            $weekBeforeBackup,
            $sinceLastBackup
        ])->sum('size');

        $fileGrowth = $recentFiles - $previousWeekFiles;
        $sizeGrowth = $recentFileSize - $previousWeekFileSize;

        // Only recommend backup if there are significant new files since last backup
        if ($recentFiles > 10 || $recentFileSize > 10485760) { // 10MB in bytes
            return [
                'type' => 'info',
                'title' => 'New Files Since Last Backup',
                'message' => "{$recentFiles} new files ({$this->formatFileSize($recentFileSize)}) uploaded since your last backup. Consider creating a backup to protect this data.",
                'icon' => 'document',
                'action' => 'create_backup',
                'file_count' => $recentFiles,
                'file_size' => $recentFileSize,
                'file_size_formatted' => $this->formatFileSize($recentFileSize)
            ];
        }

        return null;
    }

    /**
     * Analyze order patterns to find optimal backup times
     */
    private function analyzeOrderPatterns()
    {
        $orders = Order::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        if ($orders->isEmpty()) {
            return null;
        }

        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = 0;
        }

        foreach ($orders as $order) {
            $hourlyData[$order->hour] = $order->count;
        }

        $lowestActivityHour = array_keys($hourlyData, min($hourlyData))[0];
        $highestActivityHour = array_keys($hourlyData, max($hourlyData))[0];

        return [
            'lowest_activity_hour' => $lowestActivityHour,
            'lowest_activity_count' => $hourlyData[$lowestActivityHour],
            'highest_activity_hour' => $highestActivityHour,
            'highest_activity_count' => $hourlyData[$highestActivityHour],
            'hourly_data' => $hourlyData
        ];
    }

    /**
     * Get data change insights since last backup
     */
    public function getDataChangeInsights()
    {
        $lastBackup = DatabaseBackup::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastBackup) {
            return [
                'message' => 'No previous backup found for comparison.',
                'changes' => [],
                'total_changes' => 0
            ];
        }

        $sinceLastBackup = $lastBackup->created_at;

        $changes = [
            'orders' => [
                'count' => Order::where('created_at', '>', $sinceLastBackup)->count(),
                'label' => 'New Orders',
                'icon' => 'shopping-cart'
            ],
            'customers' => [
                'count' => Customer::where('created_at', '>', $sinceLastBackup)->count(),
                'label' => 'New Customers',
                'icon' => 'users'
            ],
            'users' => [
                'count' => User::where('created_at', '>', $sinceLastBackup)->count(),
                'label' => 'New Users',
                'icon' => 'user'
            ],
            'restaurants' => [
                'count' => Restaurant::where('created_at', '>', $sinceLastBackup)->count(),
                'label' => 'New Restaurants',
                'icon' => 'building'
            ],
            'files' => [
                'count' => FileStorage::where('created_at', '>', $sinceLastBackup)->count(),
                'label' => 'New Files',
                'icon' => 'document',
                'size' => FileStorage::where('created_at', '>', $sinceLastBackup)->sum('size'),
                'size_formatted' => $this->formatFileSize(FileStorage::where('created_at', '>', $sinceLastBackup)->sum('size'))
            ]
        ];

        $totalChanges = array_sum(array_column($changes, 'count'));

        // Determine if backup is recommended based on changes
        $backupRecommended = $this->isBackupRecommended($changes, $totalChanges);

        return [
            'last_backup' => $lastBackup->created_at,
            'days_since_backup' => $this->formatTimeSinceBackup($lastBackup->created_at),
            'changes' => $changes,
            'total_changes' => $totalChanges,
            'backup_recommended' => $backupRecommended,
            'recommendation_reason' => $this->getBackupRecommendationReason($changes, $totalChanges),
            'message' => $this->getBackupRecommendationReason($changes, $totalChanges)
        ];
    }

    /**
     * Format time since last backup in a user-friendly way
     */
    private function formatTimeSinceBackup($lastBackupTime)
    {
        $now = now();

        // Use Carbon's diffForHumans with absolute option
        $diffForHumans = $lastBackupTime->diffForHumans([
            'parts' => 2,
            'absolute' => true
        ]);

        // If the backup is in the future (timezone issue), show "just now"
        if ($lastBackupTime->isAfter($now)) {
            return 'just now';
        }

        return $diffForHumans;
    }

    /**
     * Determine if a backup is recommended based on data changes
     */
    private function isBackupRecommended($changes, $totalChanges)
    {
        // Recommend backup if:
        // 1. More than 100 total changes
        // 2. More than 50 new orders
        // 3. More than 10 new customers
        // 4. More than 5 new users
        // 5. More than 20 new files
        // 6. More than 100MB of new files

        return $totalChanges > 100 ||
            $changes['orders']['count'] > 50 ||
            $changes['customers']['count'] > 10 ||
            $changes['users']['count'] > 5 ||
            $changes['files']['count'] > 20 ||
            ($changes['files']['size'] ?? 0) > 104857600; // 100MB in bytes
    }

    /**
     * Get the reason for backup recommendation
     */
    private function getBackupRecommendationReason($changes, $totalChanges)
    {
        $reasons = [];

        if ($changes['orders']['count'] > 0) {
            $reasons[] = "{$changes['orders']['count']} new orders";
        }

        if ($changes['customers']['count'] > 0) {
            $reasons[] = "{$changes['customers']['count']} new customers";
        }

        if ($changes['users']['count'] > 0) {
            $reasons[] = "{$changes['users']['count']} new users";
        }

        if ($changes['files']['count'] > 0) {
            $reasons[] = "{$changes['files']['count']} new files ({$changes['files']['size_formatted']})";
        }

        if (empty($reasons)) {
            return "Minimal data changes detected since last backup.";
        }

        return "Changes since last backup: " . implode(', ', $reasons);
    }

    /**
     * Get backup health score (0-100)
     */
    public function getBackupHealthScore()
    {
        $score = 100;
        $issues = [];

        $settings = DatabaseBackupSetting::getSettings();
        $lastBackup = DatabaseBackup::where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if backups are enabled
        if (!$settings->is_enabled) {
            $score -= 30;
            $issues[] = 'Backups are disabled';
        }

        // Check last backup age
        if ($lastBackup) {
            $daysSinceLastBackup = $lastBackup->created_at->diffInDays(now());

            if ($daysSinceLastBackup > 7) {
                $score -= 25;
                $issues[] = "No backup in {$daysSinceLastBackup} days";
            } elseif ($daysSinceLastBackup > 3) {
                $score -= 10;
                $issues[] = "Backup is {$daysSinceLastBackup} days old";
            }
        } else {
            $score -= 40;
            $issues[] = 'No backups found';
        }

        // Check for failed backups
        $failedBackups = DatabaseBackup::where('status', 'failed')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        if ($failedBackups > 0) {
            $score -= 15;
            $issues[] = "{$failedBackups} failed backup(s) in last 7 days";
        }

        return [
            'score' => max(0, $score),
            'issues' => $issues,
            'status' => $this->getHealthStatus($score)
        ];
    }

    /**
     * Get health status based on score
     */
    private function getHealthStatus($score)
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 75) return 'good';
        if ($score >= 60) return 'fair';
        return 'poor';
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        if ($bytes > 1) {
            return $bytes . ' bytes';
        }

        if ($bytes == 1) {
            return $bytes . ' byte';
        }

        return '0 bytes';
    }
}
