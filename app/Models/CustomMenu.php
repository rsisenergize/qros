<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class CustomMenu extends BaseModel
{
    //
    use HasFactory;
    use Notifiable;

    protected $guarded = [];
}
