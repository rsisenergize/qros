<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class RestaurantTax extends BaseModel
{
    use HasFactory;
    use HasRestaurant;

    protected $table = 'restaurant_taxes';
    protected $fillable = ['restaurant_id', 'tax_id', 'tax_name'];


    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
