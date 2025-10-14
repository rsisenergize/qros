<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use App\Models\BaseModel;

class Customer extends BaseModel
{
    use HasFactory;
    use Notifiable;
    use HasRestaurant;
    use Notifiable;

    protected $guarded = ['id'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderBy('id', 'desc');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class)->orderBy('id', 'desc');
    }

    public function routeNotificationForVonage($notification)
    {
        if (!is_null($this->phone) && !is_null($this->phone_code)) {
            return '+' . $this->phone_code . $this->phone;
        }

        return null;
    }

    public function routeNotificationForMsg91($notification)
    {
        if (!is_null($this->phone) && !is_null($this->phone_code)) {
            return $this->phone_code . $this->phone;
        }

        return null;
    }

}
