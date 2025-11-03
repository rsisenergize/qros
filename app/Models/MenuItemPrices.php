<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemPrices extends BaseModel
{

    protected $table = 'menu_item_prices';

    protected $guarded = ['id'];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'order_type_id');
    }

    public function deliveryApp(): BelongsTo
    {
        return $this->belongsTo(DeliveryPlatform::class, 'delivery_app_id');
    }

    public function menuItemVariation(): BelongsTo
    {
        return $this->belongsTo(MenuItemVariation::class, 'menu_item_variation_id');
    }
}
