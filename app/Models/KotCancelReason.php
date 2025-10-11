<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class KotCancelReason extends BaseModel
{
    use HasRestaurant, HasFactory;

    protected $guarded = ['id'];
}
