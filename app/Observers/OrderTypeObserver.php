<?php

namespace App\Observers;

use App\Models\OrderType;

class OrderTypeObserver
{

    public function creating(OrderType $orderType)
    {
        if (branch() && $orderType->branch_id == null) {
            $orderType->branch_id = branch()->id;
        }
    }

}
