<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasRestaurant;
use Illuminate\Notifications\Notifiable;
use Modules\Inventory\Entities\PurchaseOrder;
// use Modules\Inventory\Database\Factories\SupplierFactory;

class Supplier extends Model
{
    use HasFactory;
    use HasRestaurant;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function orders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
