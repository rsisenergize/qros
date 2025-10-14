<?php

namespace Modules\Kitchen\Entities;

use Illuminate\Database\Eloquent\Model;

class KitchenGlobalSetting extends Model
{

    protected $table = 'kitchen_global_settings';


    protected $fillable = [
        'purchase_code',
        'supported_until',
        'banned_subdomain',
        'notify_update',
    ];
}
