<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\ExpenseCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditExpenseCategory extends Component
{
    use LivewireAlert;

    public $expenseCategory;
    public $name;
    public $description;
    public $is_active = true;
    public $showExpenseCategoryModal = false;

    public function mount()
    {
        // Add your code here
        $this->name = $this->expenseCategory->name;
        $this->description = $this->expenseCategory->description;

    }


    protected function rules()
    {
        return [
            'name' => 'required|min:2|unique:expense_categories,name,' . $this->expenseCategory->id . ',id,branch_id,' . branch()->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ];
    }

    public function save()
    {

        $expenseCategory = ExpenseCategory::findOrFail($this->expenseCategory->id);

        $expenseCategory->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active
        ]);

        $this->dispatch('hideEditExpenseCategory');

        $this->alert('success', __('messages.expenseCategoryUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-expense-category');
    }

}
