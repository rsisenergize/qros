<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Entities\InventoryStock;

class InventoryStockObserver
{
    /**
     * Handle the InventoryStock "created" event.
     */
    public function created(InventoryStock $inventorystock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "updated" event.
     */
    public function updated(InventoryStock $inventorystock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "deleted" event.
     */
    public function deleted(InventoryStock $inventorystock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "restored" event.
     */
    public function restored(InventoryStock $inventorystock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "force deleted" event.
     */
    public function forceDeleted(InventoryStock $inventorystock): void
    {
        //
    }
}
