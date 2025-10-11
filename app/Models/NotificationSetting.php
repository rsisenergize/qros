<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class NotificationSetting extends BaseModel
{
    use HasFactory;
    use HasRestaurant;

    protected $guarded = ['id'];
}
