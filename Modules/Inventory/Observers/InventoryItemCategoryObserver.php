<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Entities\InventoryItemCategory;

class InventoryItemCategoryObserver
{


    public function creating(InventoryItemCategory $inventoryitemcategory)
    {
        if (branch()) {
            $inventoryitemcategory->branch_id = branch()->id;
        }
    }
}
