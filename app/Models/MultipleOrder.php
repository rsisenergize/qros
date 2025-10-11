<?php

namespace App\Models;

use App\Traits\HasBranch;
use App\Traits\HasRestaurant;
use Illuminate\Database\Eloquent\Model;

class MultipleOrder extends Model
{
    use HasBranch;

    protected $guarded = [];


    protected $table = 'order_places';

    public function printerSetting()
    {
        return $this->belongsTo(Printer::class, 'printer_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'restaurant_id');
    }

}
