<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class PaypalPayment extends BaseModel
{
    use HasFactory;

    protected $table = 'paypal_payments';
    protected $fillable = [
        'paypal_payment_id',
        'order_id',
        'amount',
        'payment_status',
        'payment_date',
        'payment_error_response',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
