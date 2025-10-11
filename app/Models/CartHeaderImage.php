<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CartHeaderImage extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function cartHeaderSetting(): BelongsTo
    {
        return $this->belongsTo(CartHeaderSetting::class);
    }

    public  function imageUrl(): Attribute
    {
        return Attribute::get(fn(): string => $this->image_path ? asset_url_local_s3('cart_header_images/' . $this->image_path) : '');
    }
} 