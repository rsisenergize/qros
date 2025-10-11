<?php

namespace App\Livewire\Payments;

use App\Models\ExpenseCategory;
use App\Models\Expenses as ModelExpense;
use Livewire\Component;
use Livewire\WithPagination;
use App\Scopes\AvailableMenuItemScope;
use App\Models\Expenses as ModelsExpenses;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Imports\ModelManager;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class Expenses extends Component
{
    use WithPagination, LivewireAlert;

    public $showEditExpenseModal = false;
    public $showExpenseDetailsModal = false;
    public $confirmDeleteExpense = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $filterExpenseCategories;
    public $filterCategories = [];
    public $filterExpensePaymentMethods;
    public $filterPaymentMethods = [];
    public $filterDateRange;
    public $filterStartDate;
    public $filterEndDate;
    public $search;


    public $selectedExpenses;
    public $deleteExpense;
    public $viewExpenseDetails;

    public function mount()
    {
        abort_if(!in_array('Expense', restaurant_modules()), 403);
        abort_if((!user_can('Show Expense')), 403);

        $this->filterExpenseCategories = ExpenseCategory::all();
    }

    #[On('showExpensesFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function showEditExpense($id)
    {
        $this->showEditExpenseModal = true;
        $this->selectedExpenses = ModelExpense::find($id);
    }

    public function showExpenseDetails($id)
    {
         $this->showExpenseDetailsModal = true;
         $this->viewExpenseDetails = ModelExpense::find($id);
    }

    public function showDeleteMenuexpense($id)
    {
        $this->deleteExpense = $id;
        $this->confirmDeleteExpense = true;
    }

    public function deleteExpenseData($id)
    {
        ModelExpense::find($id)->delete();
        $this->confirmDeleteExpense = false;
        $this->deleteExpense = null;

        $this->alert('success', __('messages.expenseDeleted'), [
        'toast' => true,
        'position' => 'top-end',
        'showCancelButton' => false,
        'cancelButtonText' => __('app.close')
        ]);
    }

    public function clearFilters()
    {
          $this->filterCategories = [];
          $this->filterPaymentMethods = [];
          $this ->filterStartDate = '';
          $this->filterEndDate = '';
          $this->search = '';
          $this->dispatch('clearMenuItemFilter');
    }

    public function render()
    {
        $query = ModelsExpenses::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('amount', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
            $this->clearFilterButton = true;
        }


        // Apply filters
        if (!empty($this->filterCategories)) {
            $query->whereIn('expense_category_id', $this->filterCategories);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterPaymentMethods)) {
            $query->whereIn('payment_method', $this->filterPaymentMethods);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStartDate) && !empty($this->filterEndDate)) {
            $query->whereBetween('expense_date', [$this->filterStartDate, $this->filterEndDate]);
            $this->clearFilterButton = true;
        }



        $expenses = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.payments.expenses', ['expenses' => $expenses]);
    }

}
