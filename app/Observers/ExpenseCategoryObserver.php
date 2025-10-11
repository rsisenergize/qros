<?php

namespace App\Observers;

use App\Models\ExpenseCategory;
use App\Models\Expenses;

class ExpenseCategoryObserver
{

    public function creating(ExpenseCategory $expensesCategory)
    {
        if (branch()) {
            $expensesCategory->branch_id = branch()->id;
        }
    }

}

