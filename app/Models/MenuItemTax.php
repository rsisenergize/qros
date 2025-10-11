<?php

namespace App\Models;

use App\Models\Tax;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MenuItemTax extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
