<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class Reservation extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];

    protected $casts = [
        'reservation_date_time' => 'datetime'
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
