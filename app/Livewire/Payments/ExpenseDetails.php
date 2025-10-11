<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use Illuminate\Support\Str;

class ExpenseDetails extends Component
{
    public $expense_id;
    public $expense_title;
    public $expense_category_id;
    public $amount;
    public $expense_date;
    public $payment_status;
    public $payment_date;
    public $payment_due_date;
    public $payment_method;
    public $description;
    public $existingReceiptUrl;
    public $expense_category;
    public $expenses; // Assuming $expenses is passed or set elsewhere

    public function mount()
    {
           $this->expense_id = $this->expenses->id;
           $this->expense_title = Str::title($this->expenses->expense_title); // Convert to title case
           $this->amount = $this->expenses->amount;
           $this->expense_date = optional($this->expenses->expense_date)->format('Y-m-d');
           $this->payment_status = $this->expenses->payment_status;
           $this->payment_date = optional($this->expenses->payment_date)->format('Y-m-d');
           $this->payment_due_date = optional($this->expenses->payment_due_date)->format('Y-m-d');
           $this->payment_method = $this->expenses->payment_method;
           $this->description = $this->expenses->description;
           $this->expense_category = $this->expenses->category->name;
        // Set receipt path if exists
           $this->existingReceiptUrl = $this->expenses->expense_receipt_url;
        // Adjust based on your database field
    }

    public function render()
    {

        return view('livewire.payments.expense-details');
    }

}
