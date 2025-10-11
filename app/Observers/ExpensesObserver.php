<?php

namespace App\Observers;

use App\Models\Expenses;

class ExpensesObserver
{

    public function creating(Expenses $expenses)
    {
        if (branch()) {
            $expenses->branch_id = branch()->id;
        }
    }

}

