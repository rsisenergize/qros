<?php

namespace App\Models;

use App\Models\BaseModel;

class MenuItemTranslation extends BaseModel
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
