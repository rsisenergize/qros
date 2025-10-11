<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class Kot extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];

    public function items(): HasMany
    {
        return $this->hasMany(KotItem::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function kotPlace(): BelongsTo
    {
        return $this->belongsTo(KotPlace::class, 'kitchen_place_id');
    }

    public function cancelReason(): BelongsTo
    {
        return $this->belongsTo(KotCancelReason::class, 'cancel_reason_id');
    }

    public static function generateKotNumber($branch)
    {
        $lastKot = Kot::where('branch_id', $branch->id)->latest()->first();

        if ($lastKot) {
            return (((int)$lastKot->kot_number) + 1);
        }

        return 1;
    }
}
