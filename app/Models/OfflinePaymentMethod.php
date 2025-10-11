<?php

namespace App\Models;

use App\Models\BaseModel;

class OfflinePaymentMethod extends BaseModel
{
    protected $fillable = ['name', 'description', 'status'];

    public function offlinePlanChanges()
    {
        return $this->hasMany(OfflinePlanChange::class, 'offline_method_id');
    }
}
