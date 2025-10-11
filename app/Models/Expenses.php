<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\BaseModel;

class Expenses extends BaseModel
{
    use HasBranch;
    protected $table = 'expenses';

    protected $guarded = ['id'];

    protected $casts = [
        'expense_date' => 'date',
        'payment_date' => 'date',
        'payment_due_date' => 'date',
        'amount' => 'decimal:2'
    ];

    protected $appends = [
        'expense_receipt_url',
    ];

    public function expenseReceiptUrl(): Attribute
    {
        return Attribute::get(fn(): string => $this->receipt_path ? asset_url_local_s3('expense/' . $this->receipt_path) : (''));
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
