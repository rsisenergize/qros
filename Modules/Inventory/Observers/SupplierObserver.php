<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Entities\Supplier;

class SupplierObserver
{

    public function creating(Supplier $supplier)
    {
        if (restaurant()) {
            $supplier->restaurant_id = restaurant()->id;
        }
    }
}
