<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class DeliveryFeeTier extends BaseModel
{
    use HasBranch;

    protected $guarded = ['id'];

    protected $casts = [
        'min_distance' => 'float',
        'max_distance' => 'float',
        'fee' => 'float'
    ];
}
