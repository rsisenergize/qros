<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use Livewire\Component;
use App\Models\ExpenseCategory;
use App\Models\Expenses;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class EditExpense extends Component
{

    use WithFileUploads, LivewireAlert;
    public $expense_id;
    public $expense_category_id;
    public $expenses;
    public $amount;
    public $expense_date;
    public $payment_status = 'pending';
    public $payment_date;
    public $payment_due_date;
    public $payment_method;
    public $description;
    public $expense_receipt;
    public $receipt;
    public $newCategoryName;
    public $expense_title;
    public $showExpenseCategoryModal = false;
    public $closeModal = false;
    public $showAddExpenses = false;
    public $existingReceiptUrl;
    public $paymentMethods = [
        'cash' => 'modules.expenses.methods.cash',
        'bank_transfer' => 'modules.expenses.methods.bank_transfer',
        'credit_card' => 'modules.expenses.methods.credit_card',
        'debit_card' => 'modules.expenses.methods.debit_card',
        'check' => 'modules.expenses.methods.check',
        'digital_wallet' => 'modules.expenses.methods.digital_wallet'
    ];

    protected $listeners = [
        'expenseCategoryAdded',

    ];

    public function mount()
    {
        $this->expense_id = $this->expenses->id;
        $this->expense_title = $this->expenses->expense_title;
        $this->expense_category_id = $this->expenses->expense_category_id;
        $this->amount = $this->expenses->amount;
        $this->expense_date = optional($this->expenses->expense_date)->format('Y-m-d');
        $this->payment_status = $this->expenses->payment_status;
        $this->payment_date = optional($this->expenses->payment_date)->format('Y-m-d');
        $this->payment_due_date = optional($this->expenses->payment_due_date)->format('Y-m-d');
        $this->payment_method = $this->expenses->payment_method;
        $this->description = $this->expenses->description;

        // Set receipt path if exists
        $this->existingReceiptUrl = $this->expenses->expense_receipt_url;
        // Adjust based on your database field
    }

    public function save()
    {
        $this->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_method' => $this->payment_status === 'paid' ? 'required|string' : 'nullable|string',
            'description' => 'nullable|string|max:1000',
            'expense_receipt' => 'nullable|file|max:5120', // Optional & supports any file up to 5MB
        ]);

        $expense = Expenses::findOrFail($this->expense_id);
        $expense->update([
            'expense_category_id' => $this->expense_category_id,
            'expense_title' => $this->expense_title,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date ? \Carbon\Carbon::parse($this->expense_date) : null,
            'payment_status' => $this->payment_status,
            'payment_date' => $this->payment_date ? \Carbon\Carbon::parse($this->payment_date) : null,
            'payment_due_date' => $this->payment_due_date ? \Carbon\Carbon::parse($this->payment_due_date) : null,
            'payment_method' => $this->payment_method,
            'description' => $this->description,
        ]);


        // Handle receipt upload if a new one is provided
        if ($this->expense_receipt) {
            $receiptPath = Files::uploadLocalOrS3($this->expense_receipt, 'expense');
            $expense->update(['receipt_path' => $receiptPath]);
        }

        $this->dispatch('expenseUpdated');

        $this->alert('success', __('messages.expenseUpdated'), [
            'toast' => true,
            'position' => 'top-end',
            'showCancelButton' => false,
            'cancelButtonText' => __('app.close')
        ]);
    }

    public function render()
    {
        return view('livewire.forms.edit-expense', [
            'categories' => ExpenseCategory::orderBy('name')->get(),
        ]);
    }
}
