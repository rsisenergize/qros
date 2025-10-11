<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class KotItemModifierOption extends BaseModel
{
    protected $guarded = ['id'];

    public function kotItem(): BelongsTo
    {
        return $this->belongsTo(KotItem::class, 'kot_item_id');
    }

    public function modifierOption(): BelongsTo
    {
        return $this->belongsTo(ModifierOption::class, 'modifier_option_id');
    }
}
