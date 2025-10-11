<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class RestaurantCharge extends BaseModel
{
    use HasFactory;
    use HasRestaurant;

    protected $guarded = ['id'];

    protected $casts = [
        'order_types' => 'array',
    ];


    public function getAmount($amount)
    {
        return $this->charge_type === 'percent'
            ? ($amount * $this->charge_value) / 100
            : $this->charge_value;
    }
}
