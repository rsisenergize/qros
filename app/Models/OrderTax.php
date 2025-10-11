<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class OrderTax extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
