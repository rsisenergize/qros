<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class GlobalSetting extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    const FAVICON_BASE_PATH_GLOBAL = 'favicons/super-admin/';

    const FAVICONS = [
        'upload_fav_icon_android_chrome_192' => [
            'name' => 'android-chrome-192x192.png',
            'width' => 192,
            'height' => 192
        ],
        'upload_fav_icon_android_chrome_512' => [
            'name' => 'android-chrome-512x512.png',
            'width' => 512,
            'height' => 512
        ],
        'upload_fav_icon_apple_touch_icon' => [
            'name' => 'apple-touch-icon.png',
            'width' => 180,
            'height' => 180
        ],
        'upload_favicon_16' => [
            'name' => 'favicon-16x16.png',
            'width' => 16,
            'height' => 16
        ],
        'upload_favicon_32' => [
            'name' => 'favicon-32x32.png',
            'width' => 32,
            'height' => 32
        ],
        'favicon' => [
            'name' => 'favicon.ico',
            'width' => 32,
            'height' => 32
        ],
    ];

    public function getFaviconBasePath(): string
    {
        return self::FAVICON_BASE_PATH_GLOBAL;
    }

    protected $appends = [
        'logo_url',
    ];

    protected $casts = [
        'purchased_on' => 'datetime',
        'supported_until' => 'datetime',
        'last_license_verified_at' => 'datetime',
        'last_cron_run' => 'datetime',
    ];

    public function logoUrl(): Attribute
    {
        return Attribute::get(fn(): string => $this->logo ? asset_url_local_s3('logo/' . $this->logo) : asset('img/logo.png'));
    }

    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(GlobalCurrency::class, 'default_currency_id');
    }


    /**
     * Get URL for Android Chrome 192x192 favicon
     * Returns custom favicon if available, otherwise falls back to default
     */
    public function uploadFavIconAndroidChrome192Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom favicon if exists, otherwise use default
            return $this->upload_fav_icon_android_chrome_192
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_android_chrome_192)
                : asset('img/favicons/android-chrome-192x192.png');
        });
    }

    /**
     * Get URL for Android Chrome 512x512 favicon
     * Returns custom favicon if available, otherwise falls back to default
     */
    public function uploadFavIconAndroidChrome512Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom favicon if exists, otherwise use default
            return $this->upload_fav_icon_android_chrome_512
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_android_chrome_512)
                : asset('img/favicons/android-chrome-512x512.png');
        });
    }

    /**
     * Get URL for Apple Touch Icon (180x180)
     * Returns custom icon if available, otherwise falls back to default
     */
    public function uploadFavIconAppleTouchIconUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom icon if exists, otherwise use default
            return $this->upload_fav_icon_apple_touch_icon
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_fav_icon_apple_touch_icon)
                : asset('img/favicons/apple-touch-icon.png');
        });
    }

    /**
     * Get URL for 16x16 favicon
     * Returns custom favicon if available, otherwise falls back to default
     */
    public function uploadFavIcon16Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom favicon if exists, otherwise use default
            return $this->upload_favicon_16
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_favicon_16)
                : asset('img/favicons/favicon-16x16.png');
        });
    }

    /**
     * Get URL for 32x32 favicon
     * Returns custom favicon if available, otherwise falls back to default
     */
    public function uploadFavIcon32Url(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom favicon if exists, otherwise use default
            return $this->upload_favicon_32
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->upload_favicon_32)
                : asset('img/favicons/favicon-32x32.png');
        });
    }

    /**
     * Get URL for main favicon.ico file
     * Returns custom favicon if available, otherwise falls back to default
     */
    public function faviconUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom favicon if exists, otherwise use default
            return $this->favicon
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->favicon)
                : asset('img/favicons/favicon.ico');
        });
    }

    /**
     * Get URL for site webmanifest file
     * Returns custom webmanifest if available, otherwise falls back to default
     */
    public function webmanifestUrl(): Attribute
    {
        return Attribute::get(function (): string {
            // Use custom webmanifest if exists, otherwise use default
            return $this->webmanifest
                ? asset_url_local_s3($this->getFaviconBasePath() . $this->webmanifest)
                : asset('img/favicons/site.webmanifest');
        });
    }
}
