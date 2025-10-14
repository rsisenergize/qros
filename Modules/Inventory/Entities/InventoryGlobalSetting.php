<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;

class InventoryGlobalSetting extends Model
{

    protected $table = 'inventory_global_settings';


    protected $fillable = [
        'purchase_code',
        'supported_until',
        'banned_subdomain',
        'notify_update',
    ];
}
