<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Restaurant;
use App\Traits\HasRestaurant;

class InventorySetting extends Model
{
    use HasFactory, HasRestaurant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'restaurant_id',
        'allow_auto_purchase',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
