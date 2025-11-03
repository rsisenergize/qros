<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryPlatform extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $path = 'delivery-apps-logo';

    protected $guarded = ['id'];

    protected $appends = [
        'logo_url',
    ];


    public function getCommissionAmount($amount)
    {
        if ($this->commission_type === 'percent') {
            return ($amount * $this->commission_value) / 100;
        }

        return $this->commission_value;
    }

    /**
     * Calculate the price including commission markup
     * 
     * @param float $basePrice The base price before commission
     * @return float The price including commission markup
     */
    public function getPriceWithCommission($basePrice)
    {
        if ($this->commission_type === 'percent') {
            // For percentage commission, we need to add the commission to the base price
            // If commission is 15%, we increase the price by 15%
            return $basePrice + (($basePrice * $this->commission_value) / 100);
        }

        // For fixed commission, add the fixed amount
        return $basePrice + $this->commission_value;
    }


    public function logoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            return $this->logo ? asset_url_local_s3($this->path . '/' . $this->logo) : null;
        });
    }


    public function getFormattedCommissionAttribute()
    {
        return $this->commission_value . ($this->commission_type === 'percent' ? '%' : '');
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
