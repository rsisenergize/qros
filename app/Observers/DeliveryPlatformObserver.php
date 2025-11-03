<?php

namespace App\Observers;

use App\Models\DeliveryPlatform;

class DeliveryPlatformObserver
{
    /**
     * Handle the DeliveryPlatform "creating" event.
     */
    public function creating(DeliveryPlatform $deliveryPlatform): void
    {
        if (branch()) {
            $deliveryPlatform->branch_id = branch()->id;
        }
    }
}
