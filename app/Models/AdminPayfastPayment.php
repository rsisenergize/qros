<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPayfastPayment extends Model
{
    use HasFactory;
    protected $table = 'payfast_payments';
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
