<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class GlobalCurrency extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'currency_name',
        'currency_symbol',
        'currency_code',
        'exchange_rate',
        'usd_price',
        'is_cryptocurrency',
        'currency_position',
        'no_of_decimal',
        'thousand_separator',
        'decimal_separator',
        'status',
    ];

    protected $casts = [
        'is_cryptocurrency' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
