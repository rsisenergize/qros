<?php

namespace Modules\Kiosk\Entities;

use App\Models\BaseModel;

class KioskGlobalSetting extends BaseModel
{

    protected $table = 'kiosk_global_settings';

    protected $guarded = ['id'];

    const MODULE_NAME = 'kiosk';
}
