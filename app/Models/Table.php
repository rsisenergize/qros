<?php

namespace App\Models;

use App\Helper\Files;
use App\Traits\HasBranch;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use App\Traits\GeneratesQrCode;
use App\Models\BaseModel;

class Table extends BaseModel
{

    use HasFactory;
    use HasBranch;
    use GeneratesQrCode;

    protected $guarded = ['id'];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function activeOrder(): HasOne
    {
        return $this->hasOne(Order::class)->whereIn('status', ['billed', 'kot'])->orderBy('id', 'desc');
    }

    public function qRCodeUrl(): Attribute
    {
        return Attribute::get(fn(): string => asset_url_local_s3('qrcodes/' . $this->getQrCodeFileName()));
    }

    public function generateQrCode()
    {
        // Generate a new hash to invalidate old QR code links
        $this->update(['hash' => md5(microtime() . rand(1, 99999999))]);

        $this->createQrCode(route('table_order', [$this->hash]), __('modules.table.table') . ' ' . str()->slug($this->table_code, '-', (auth()->user() ? auth()->user()->locale : 'en')));
    }

    public function getQrCodeFileName(): string
    {
        return 'qrcode-' . $this->branch_id . '-' . str()->slug($this->table_code, '-', (auth()->user() ? auth()->user()->locale : 'en')) . '.png';
    }

    public function getRestaurantId(): int
    {
        return $this->branch?->restaurant_id;
    }

    public function activeWaiterRequest(): HasOne
    {
        return $this->hasOne(WaiterRequest::class)->where('status', 'pending');
    }

    public function waiterRequests(): HasMany
    {
        return $this->hasMany(WaiterRequest::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->where('reservation_date_time', '>=', now())
            ->orderBy('reservation_date_time', 'asc');
    }

    public function currentReservationOrders()
    {
        return $this->hasOne(Order::class)
            ->whereHas('reservation', function ($query) {
                $activeReservation = $this->activeReservation;
                if ($activeReservation) {
                    $query->where('id', $activeReservation->id);
                }
            });
    }

    public function tableSession(): HasOne
    {
        return $this->hasOne(TableSession::class);
    }

    public function getOrCreateSession(): TableSession
    {
        return $this->tableSession()->firstOrCreate([
            'table_id' => $this->id
        ]);
    }

    public function isLocked(): bool
    {
        $session = $this->tableSession;
        return $session ? $session->isLocked() : false;
    }

    public function isLockedByUser(int $userId): bool
    {
        $session = $this->tableSession;
        return $session ? $session->isLockedByUser($userId) : false;
    }

    public function canBeAccessedByUser(int $userId, int $lockTimeoutMinutes = 5): bool
    {
        $session = $this->getOrCreateSession();
        return $session->canBeAccessedByUser($userId, $lockTimeoutMinutes);
    }

    public function lockForUser(int $userId): array
    {
        $session = $this->getOrCreateSession();

        if (!$session->canBeAccessedByUser($userId)) {
            $lockedByUser = $session->lockedByUser;
            return [
                'success' => false,
                'message' => "This table is currently being handled by {$lockedByUser->name}. Please try again later.",
                'locked_by' => $lockedByUser->name ?? 'Unknown User',
                'locked_at' => $session->locked_at?->format('H:i') ?? '',
            ];
        }

        if ($session->lockForUser($userId)) {
            return [
                'success' => true,
                'message' => 'Table locked successfully',
                'session_token' => $session->session_token,
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to lock table',
        ];
    }

    public function updateActivity(int $userId): bool
    {
        $session = $this->tableSession;

        if (!$session || !$session->isLockedByUser($userId)) {
            return false;
        }

        return $session->updateActivity();
    }

    public function unlock(int $userId = null, bool $forceUnlock = false): array
    {
        $session = $this->tableSession;

        if (!$session) {
            return [
                'success' => true,
                'message' => 'Table is not locked',
            ];
        }

        // If not force unlock, check if user can unlock
        if (!$forceUnlock && $userId && !$session->isLockedByUser($userId)) {
            return [
                'success' => false,
                'message' => 'You cannot unlock this table',
            ];
        }

        if ($session->releaseLock()) {
            return [
                'success' => true,
                'message' => 'Table unlocked successfully',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to unlock table',
        ];
    }

    /**
     * Clean up expired table locks - centralized static method
     */
    public static function cleanupExpiredLocks(): array
    {
        $lockTimeoutMinutes = restaurant()->table_lock_timeout_minutes ?? 10;
        $expiredTime = now()->subMinutes($lockTimeoutMinutes);

        // Using the model instance to ensure branch scope is applied
        $expiredSessions = TableSession::with(['table', 'lockedByUser'])
            ->where('last_activity_at', '<', $expiredTime)
            ->whereNotNull('locked_by_user_id')
            ->get();

        // Cleanup expired locks - also respecting branch scope
        $affectedRows = TableSession::where('last_activity_at', '<', $expiredTime)
            ->whereNotNull('locked_by_user_id')
            ->update([
                'locked_by_user_id' => null,
                'locked_at' => null,
                'last_activity_at' => null,
                'session_token' => null,
            ]);

        return [
            'affected_rows' => $affectedRows,
            'expired_sessions' => $expiredSessions->toArray(),
        ];
    }

    /**
     * Get currently locked tables data for display
     */
    public static function getLockedTablesData(): array
    {
        // Auto-cleanup expired locks first
        self::cleanupExpiredLocks();

        $totalLocked = TableSession::whereNotNull('locked_by_user_id')->count();

        $lockTimeout = restaurant()->table_lock_timeout_minutes ?? 10;
        $expiredTime = now()->subMinutes($lockTimeout);

        $expiredLocks = TableSession::whereNotNull('locked_by_user_id')
            ->where('last_activity_at', '<', $expiredTime)
            ->count();

        $lockedTables = TableSession::with(['table.area', 'lockedByUser'])
            ->whereNotNull('locked_by_user_id')
            ->whereNotNull('locked_at')
            ->orderBy('locked_at', 'desc')
            ->get()
            ->toArray();

        return [
            'total_locked' => $totalLocked,
            'expired_locks' => $expiredLocks,
            'locked_tables' => $lockedTables,
        ];
    }
}
