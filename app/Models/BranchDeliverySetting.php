<?php

namespace App\Models;

use App\Traits\HasBranch;
use App\Enums\DeliveryFeeType;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchDeliverySetting extends BaseModel
{
    use HasBranch;

    protected $guarded = ['id'];
    protected $casts = [
        'fee_type' => DeliveryFeeType::class,
        'max_radius' => 'float',
        'fixed_fee' => 'float',
        'per_distance_rate' => 'float',
        'free_delivery_over_amount' => 'float',
        'free_delivery_within_radius' => 'float'
    ];


    /**
     * Get the max_radius attribute
     *
     * @param float $value
     * @return float
     */
    public function getMaxRadiusAttribute($value)
    {
        $distanceUnit = $this->distance_unit ?? 'km';

        if ($distanceUnit === 'miles') {
            // Convert miles to kilometers for display/use
            return $value * 1.60934;
        }

        return $value;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function feeRangeTiers(): HasMany
    {
        return $this->hasMany(DeliveryFeeTier::class, 'branch_id', 'branch_id');
    }
}
