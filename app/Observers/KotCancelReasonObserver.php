<?php

namespace App\Observers;

use App\Models\KotCancelReason;
use App\Models\Table;

class KotCancelReasonObserver
{

    public function creating(KotCancelReason $table)
    {
        if (restaurant()) {
            $table->restaurant_id = restaurant()->id;
        }
    }

}
