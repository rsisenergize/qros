<?php

namespace App\Models;

use App\Scopes\AvailableMenuItemScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class ItemModifier extends BaseModel
{
    protected $guarded = ['id'];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class)->withoutGlobalScope(AvailableMenuItemScope::class);
    }

    public function modifierGroup(): BelongsTo
    {
        return $this->belongsTo(ModifierGroup::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(MenuItemVariation::class, 'menu_item_variation_id');
    }
}
