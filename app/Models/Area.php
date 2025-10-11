<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class Area extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }
}
