<?php

namespace App\Livewire\Payments;

use Livewire\Attributes\On;
use Livewire\Component;

class ExpensesContent extends Component
{

    public $search;
    public $showAddExpenses = false;
    public $showFilterButton = true;
    public $closeModal = false;

    #[On('hideAddExpenses')]
    public function hideAddExpenses()
    {
        $this->showAddExpenses = false;
    }

    #[On('clearExpenseFilter')]
    public function clearExpenseFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    public function render()
    {
        return view('livewire.payments.expenses-content');
    }

}
