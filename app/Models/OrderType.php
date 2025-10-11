<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    use HasBranch;

    protected $guarded = ['id'];

}
