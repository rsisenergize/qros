<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Traits\HasBranch;

class OrderNumberSetting extends BaseModel
{

    use HasBranch;

    protected $guarded = ['id'];


    // Cast attributes to proper types
    protected $casts = [
        'enable_feature' => 'boolean',
        'digits' => 'integer',
        'include_date' => 'boolean',
        'show_year' => 'boolean',
        'show_month' => 'boolean',
        'show_day' => 'boolean',
        'show_time' => 'boolean',
        'reset_daily' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
