<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;


class Contact extends BaseModel
{
    protected $table = 'contacts';

    protected $guarded = ['id'];

    protected $appends = [
        'image_url',
    ];

    public function imageUrl(): Attribute
    {

        return Attribute::get(fn(): string => $this->image ? asset_url_local_s3('contact_image/' . $this->image) : asset('landing/contact-image.png'));
    }
}
