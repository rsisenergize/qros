<?php

namespace App\Observers;

use App\Models\Kot;
use App\Models\KotSetting;
use App\Events\KotUpdated;

class KotObserver
{
    public function creating(Kot $kot)
    {
        $kotSettings = KotSetting::first();

        if (branch() && $kot->branch_id == null) {
            $kot->branch_id = branch()->id;
        }

        if ($kot->order?->order_status->value === 'placed' || $kotSettings->default_status == 'pending') {
            $kot->status = 'pending_confirmation';
        } elseif ($kotSettings->default_status == 'cooking') {
            $kot->status = 'in_kitchen';
        }
    }

    public function saved(Kot $kot)
    {

        event(new KotUpdated($kot));
    }
}
