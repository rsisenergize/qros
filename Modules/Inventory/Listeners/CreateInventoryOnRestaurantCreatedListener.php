<?php

namespace Modules\Inventory\Listeners;

use App\Events\NewRestaurantCreatedEvent;
use Modules\Inventory\Entities\InventorySetting;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\Restaurant;
use App\Models\Module;

class CreateInventoryOnRestaurantCreatedListener
{
    public function handle(NewRestaurantCreatedEvent $event): void
    {
        $restaurant = $event->restaurant;

        InventorySetting::firstOrCreate([
            'restaurant_id' => $restaurant->id,
        ]);

        // Other branch related settings are inserted in BranchObserver of inventory module
    }
}
