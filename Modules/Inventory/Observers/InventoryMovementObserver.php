<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Entities\InventoryMovement;

class InventoryMovementObserver
{

    public function creating(InventoryMovement $inventorymovement)
    {
        if (branch()) {
            $inventorymovement->branch_id = branch()->id;
        }

        if (user()) {
            $inventorymovement->added_by = user()->id;
        }
    }

}
