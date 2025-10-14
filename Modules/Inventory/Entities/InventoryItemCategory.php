<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranch;

class InventoryItemCategory extends Model
{
    use HasFactory;
    use HasBranch;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    const CATEGORIES = [
        'Meat & Poultry',
        'Seafood',
        'Dairy & Eggs',
        'Fresh Produce',
        'Herbs & Spices',
        'Dry Goods',
        'Canned Goods',
        'Beverages',
        'Condiments & Sauces',
        'Baking Supplies',
        'Oils & Vinegars',
        'Frozen Foods',
        'Cleaning Supplies',
        'Kitchen Equipment',
        'Disposables'
    ];
}
