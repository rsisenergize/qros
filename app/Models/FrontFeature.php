<?php

namespace App\Models;

use Carbon\Language;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;


class FrontFeature extends BaseModel
{
    protected $table = 'front_features';

    protected $guarded = ['id'];

    protected $fillable = ['language_setting_id', 'title', 'description', 'type', 'icon', 'image']; // Ensure 'image' is fillable

    protected $appends = [
        'image_url',
    ];

    public function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->type === 'image') {
                if ($this->image) {
                    return asset_url_local_s3('front_feature/' . $this->image);
                }

                $defaults = [
                    'Streamline Order Management'   => asset('landing/order-management.png'),
                    'Optimize Table Reservations'   => asset('landing/table-reservation.png'),
                    'Effortless Menu Management'    => asset('landing/order-management.png'),
                ];

                return $defaults[$this->title] ?? '';
            }

            return '';
        });
    }

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }
}
