<?php

namespace App\Observers;

use App\Models\KotPlace;

class KotPlaceObserver
{

    public function creating(KotPlace $kot_place)
    {
        if (branch() && $kot_place->branch_id == null) {
            $kot_place->branch_id = branch()->id;
        }
    }
}
