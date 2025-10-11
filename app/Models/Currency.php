<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Currency extends BaseModel
{

    use HasFactory, HasRestaurant;

    public $timestamps = false;
}
