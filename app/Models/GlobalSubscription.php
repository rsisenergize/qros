<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class GlobalSubscription extends BaseModel
{

    protected $guarded = ['id'];

    protected $casts = [
        'ends_at' => 'datetime',
        'subscribed_on' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
