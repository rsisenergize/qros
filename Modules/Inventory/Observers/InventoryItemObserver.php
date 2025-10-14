<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Entities\InventoryItem;

class InventoryItemObserver
{

    public function creating(InventoryItem $inventoryitem)
    {
        if (branch()) {
            $inventoryitem->branch_id = branch()->id;
        }
    }
}
