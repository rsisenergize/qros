<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class DeliveryExecutive extends BaseModel
{
    use HasBranch;

    protected $guarded = ['id'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderBy('id', 'desc');
    }
}
