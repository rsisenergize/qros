<?php

namespace App\Observers;

use App\Models\KotItem;
use App\Events\KotUpdated;

class KotItemObserver
{

    public function saved(KotItem $kotItem)
    {
        event(new KotUpdated($kotItem->kot));
    }

    public function deleted(KotItem $kotItem)
    {
        event(new KotUpdated($kotItem->kot));
    }
}
