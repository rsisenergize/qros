<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\HasBranch;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModifierGroup extends BaseModel
{
    use HasFactory, HasBranch, HasTranslations;

    protected $guarded = ['id'];

    protected $with = ['translations'];

    public function translations(): HasMany
    {
        return $this->hasMany(ModifierGroupTranslation::class, 'modifier_group_id');
    }

    public function translation($locale = null): HasOne
    {
        return $this->hasOne(ModifierGroupTranslation::class)->where('locale', $locale ?? app()->getLocale());
    }

    public function getTranslatedValue(string $attribute, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);
        return $translation->{$attribute} ?? $this->attributes[$attribute] ?? '';
    }

    public function getNameAttribute(): string
    {
        return $this->getTranslatedValue('name');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getTranslatedValue('description');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ModifierOption::class, 'modifier_group_id');
    }

    public function itemModifiers(): HasMany
    {
        return $this->hasMany(ItemModifier::class);
    }
}
