<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\BaseModel;

class ReservationSetting extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];

    protected $casts = [
        'time_slot_difference' => 'integer',
    ];
}
