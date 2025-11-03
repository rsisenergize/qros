<?php

namespace App\Models;

use App\Traits\HasBranch;
use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Printer extends Model
{
    use HasBranch {
        HasBranch::booted insteadof HasRestaurant;
    }
    use HasRestaurant {
        HasRestaurant::booted as hasRestaurantBooted;
    }

    protected $casts = [
        'kots' => 'array',
        'orders' => 'array',
    ];
    protected $guarded = ['id'];

    protected $appends = [
        'printer_connected',
        'kot_details',
        'order_details',
    ];

    public function getKotDetailsAttribute()
    {
        $kots = $this->kots; // [1,11]

        if (is_array($kots)) {
            $kotPlaces = KotPlace::whereIn('id', $kots)->get();
        } else {
            $kotPlaces = KotPlace::where('id', $kots)->get();
        }

        return $kotPlaces;
    }

    public function getOrderDetailsAttribute()
    {
        $orders = $this->orders; // [1,11]

        if (is_array($orders)) {
            $orders = MultipleOrder::whereIn('id', $orders)->get();
        } else {
            $orders = MultipleOrder::where('id', $orders)->get();
        }

        return $orders;
    }


    public function orders()
    {
        return $this->hasMany(MultipleOrder::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withoutGlobalScopes();
    }

    public function printerConnected(): Attribute
    {

        return Attribute::get(function (): string {
            return false;
        });
    }

    public static function getPrintWidth($printerSetting = null)
    {

        return match ($printerSetting?->print_format ?? 'thermal80mm') {
            'thermal56mm' => 56,
            'thermal112mm' => 112,
            default => 80,
        };
    }
}
