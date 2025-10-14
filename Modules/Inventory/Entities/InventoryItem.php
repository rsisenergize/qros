<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranch;
use Modules\Inventory\Entities\InventoryItemCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MenuItem;
// use Modules\Inventory\Database\Factories\InventoryItemFactory;

class InventoryItem extends Model
{
    use HasFactory;
    use HasBranch;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'branch_id',
        'name',
        'inventory_item_category_id',
        'unit_id',
        'unit_purchase_price',
        'threshold_quantity',
        'preferred_supplier_id',
        'reorder_quantity'
    ];



    public function category()
    {
        return $this->belongsTo(InventoryItemCategory::class, 'inventory_item_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->stocks()->sum('quantity');
    }

    public function getStockStatus()
    {
        $currentStock = $this->current_stock;

        if ($currentStock <= 0) {
            return [
                'status' => 'Out of Stock',
                'class' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
            ];
        } elseif ($currentStock <= $this->threshold_quantity) {
            return [
                'status' => 'Low Stock',
                'class' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'
            ];
        }

        return [
            'status' => 'In Stock',
            'class' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
        ];
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'recipes', 'inventory_item_id', 'menu_item_id');
    }
}
