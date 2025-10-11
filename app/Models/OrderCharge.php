<?php

namespace App\Models;

use App\Models\BaseModel;

class OrderCharge extends BaseModel
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function charge()
    {
        return $this->belongsTo(RestaurantCharge::class, 'charge_id');
    }
}
