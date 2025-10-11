<?php

namespace App\Models;

use App\Models\BaseModel;

class SplitOrderItem extends BaseModel
{
    protected $fillable = ['split_order_id', 'order_item_id', 'quantity'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function splitOrder()
    {
        return $this->belongsTo(SplitOrder::class, 'split_order_id');
    }
}
