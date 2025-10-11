<?php

namespace App\Models;

use App\Models\BaseModel;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModifierOption extends BaseModel
{
    use HasFactory, HasTranslations;

    protected $guarded = ['id'];

    public $translatable = ['name'];

    public function modifierGroup(): BelongsTo
    {
        return $this->belongsTo(ModifierGroup::class, 'modifier_group_id');
    }

    public function orderItemModifierOptions(): HasMany
    {
        return $this->hasMany(OrderItemModifierOption::class, 'modifier_option_id');
    }

    public function kotItemModiferOptions(): HasMany
    {
        return $this->hasMany(KotItemModifierOption::class, 'modifier_option_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(\Modules\Inventory\Entities\Recipe::class, 'modifier_option_id');
    }
}
