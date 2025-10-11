<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class CustomerAddress extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
