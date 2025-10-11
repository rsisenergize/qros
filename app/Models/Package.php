<?php

namespace App\Models;

use App\Enums\PackageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Package extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'package_type' => PackageType::class,
        'trial_days' => 'integer',
        'trial_notification_before_days' => 'integer',
    ];

    const ADDITIONAL_FEATURES = [
        'Change Branch',
        'Export Report',
        'Table Reservation',
        'Payment Gateway Integration',
        'Theme Setting',
        'Customer Display',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'package_modules');
    }

    public function currency()
    {
        return $this->belongsTo(GlobalCurrency::class, 'currency_id');
    }

    public function hasModule($moduleId)
    {
        return $this->modules()->where('module_id', $moduleId)->exists();
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'package_id');
    }
}
