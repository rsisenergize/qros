<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class XenditPayment extends BaseModel
{
    use HasFactory;

    protected $table = 'xendit_payments';
    protected $fillable = [
        'xendit_payment_id',
        'order_id',
        'amount',
        'payment_status',
        'payment_date',
        'payment_error_response',
        'xendit_invoice_id',
        'xendit_external_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
