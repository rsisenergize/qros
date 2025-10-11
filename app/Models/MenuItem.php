<?php

namespace App\Models;

use App\Models\Menu;
use App\Models\OrderItem;
use App\Traits\HasBranch;
use App\Models\ItemCategory;
use App\Models\MenuItemVariation;
use App\Models\MenuItemTranslation;
use Illuminate\Support\Facades\Cache;
use App\Scopes\AvailableMenuItemScope;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\BaseModel;

class MenuItem extends BaseModel
{
    use HasFactory, HasBranch, HasTranslations;


    const VEG = 'veg';
    const NONVEG = 'non-veg';
    const EGG = 'egg';

    const FILENAME_TO_EXCLUDE = [
        '.htaccess',
        'butter-chicken.webp',
        'chicken-hyderabadi-biryani.webp',
        'chicken-manchurian.webp',
        'chilli-paneer.webp',
        'dal-makhni.webp',
        'idli-sambar.webp',
        'masala-dosa.webp',
        'medu-vada.webp',
        'naan-recipe.webp',
        'paneer-tikka.webp',
        'spring-rolls.webp',
        'tandoori-roti.webp',
        'uttapam.webp',
        'vegetable-hakka-noodles.webp',
        'vegetable-manchow-soup.webp'
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'show_on_customer_site' => 'boolean',
    ];

    protected $appends = [
        'item_photo_url',
    ];

    protected $with = ['translations'];



    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new AvailableMenuItemScope());
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MenuItemTranslation::class, 'menu_item_id');
    }

    public function translation($locale = null): HasOne
    {
        return $this->hasOne(MenuItemTranslation::class)->where('locale', $locale ?? app()->getLocale());
    }

    public function getTranslatedValue(string $attribute, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = "menu_item_{$this->id}_{$attribute}_{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($locale, $attribute) {
            $translation = $this->translation($locale)->first();
            return $translation?->{$attribute} ?? $this->attributes[$attribute] ?? '';
        });
    }

    public function getItemNameAttribute(): string
    {
        return $this->getTranslatedValue('item_name');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getTranslatedValue('description');
    }

    public function itemPhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if (in_array($this->image, MenuItem::FILENAME_TO_EXCLUDE)) {
                return asset_url('item/' . $this->image);
            }
            return $this->image ? asset_url_local_s3('item/' . $this->image) : asset('img/food.svg');
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(MenuItemVariation::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(\Modules\Inventory\Entities\Recipe::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(ItemModifier::class);
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class, 'item_modifiers', 'menu_item_id', 'modifier_group_id');
    }

    public function kotPlace()
    {
        return $this->belongsTo(KotPlace::class, 'kot_places_id');
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class, 'menu_item_tax', 'menu_item_id', 'tax_id');
    }

    public function getTaxBreakdown($price, $selectedTaxIds = [], $isInclusive = null)
    {
        if (restaurant()->tax_mode !== 'item' || !$price) {
            return null;
        }

        if (empty($selectedTaxIds)) {
            return null;
        }

        $taxes = Tax::whereIn('id', $selectedTaxIds)->get();
        $taxPercent = $taxes->sum('tax_percent');
        $basePrice = floatval($price);

        // Use passed inclusive setting if provided, otherwise use restaurant setting
        // $inclusive = $isInclusive !== null ? $isInclusive : restaurant()->tax_inclusive;
        $inclusive = $isInclusive ?? restaurant()->tax_inclusive;

        $taxBreakdown = [];
        if ($inclusive) {
            $base = $basePrice / (1 + $taxPercent / 100);
            $totalTax = $basePrice - $base;

            // Calculate individual tax amounts
            foreach ($taxes as $tax) {
                $amount = $base * ($tax->tax_percent / 100);
                $taxBreakdown[$tax->tax_name] = $amount;
            }
        } else {
            $base = $basePrice;
            $totalTax = 0;

            // Calculate individual tax amounts
            foreach ($taxes as $tax) {
                $amount = $base * ($tax->tax_percent / 100);
                $taxBreakdown[$tax->tax_name] = $amount;
                $totalTax += $amount;
            }
        }

        return [
            'base' => currency_format($base, restaurant()->currency_id),
            'base_raw' => $base,
            'tax' => currency_format($totalTax, restaurant()->currency_id),
            'tax_raw' => $totalTax,
            'total' => currency_format($base + $totalTax, restaurant()->currency_id),
            'total_raw' => $base + $totalTax,
            'tax_percent' => $taxPercent,
            'inclusive' => $inclusive,
            'tax_breakdown' => $taxBreakdown
        ];
    }

    public static function calculateItemTaxes($itemPrice, $taxes = [], $inclusive)
    {
        // Ensure $taxes is a collection
        if (is_array($taxes)) {
            $taxes = collect($taxes);
        }

        $taxPercent = $taxes->sum('tax_percent');
        $basePrice = floatval($itemPrice);

        $taxBreakdown = [];
        $totalTax = 0;

        if ($inclusive) {
            $base = $basePrice / (1 + $taxPercent / 100);
            $totalTax = $basePrice - $base;

            foreach ($taxes as $tax) {
                $amount = $base * ($tax->tax_percent / 100);
                $taxBreakdown[$tax->tax_name] = [
                    'percent' => $tax->tax_percent,
                    'amount' => round($amount, 2)
                ];
                // No need to add to totalTax here as it's already calculated above
            }
        } else {
            $base = $basePrice;
            $totalTax = 0;

            foreach ($taxes as $tax) {
                $amount = $base * ($tax->tax_percent / 100);
                $taxBreakdown[$tax->tax_name] = [
                    'percent' => $tax->tax_percent,
                    'amount' => round($amount, 2)
                ];
                $totalTax += $amount;
            }
        }

        return [
            'base' => $base,
            'tax_amount' => $totalTax,
            'tax_percentage' => $taxPercent,
            'total_amount' => $base + $totalTax,
            'inclusive' => $inclusive,
            'tax_breakdown' => $taxBreakdown
        ];
    }
}
