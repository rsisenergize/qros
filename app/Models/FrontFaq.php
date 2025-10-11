<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;


class FrontFaq extends BaseModel
{
    protected $table = 'front_faq_settings';

    protected $guarded = ['id'];
}
