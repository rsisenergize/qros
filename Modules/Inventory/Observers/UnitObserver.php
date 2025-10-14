<?php

namespace Modules\Inventory\Observers;

use Modules\Inventory\Entities\Unit;

class UnitObserver
{

    public function creating(Unit $unit)
    {
        if (branch()) {
            $unit->branch_id = branch()->id;
        }
    }
}
