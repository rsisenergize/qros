<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class EmailSetting extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];
}
