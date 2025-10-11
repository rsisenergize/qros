<?php

namespace App\Observers;

use App\Models\Printer;

class PrinterObserver
{

    public function creating(Printer $printer)
    {
        if (branch() && $printer->branch_id == null) {
            $printer->restaurant_id = restaurant()->id;
            $printer->branch_id = branch()->id;
        }
    }
}
