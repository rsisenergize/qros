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

}
