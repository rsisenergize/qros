<?php

namespace App\Models;

use App\Traits\HasBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\BaseModel;

class OrderItem extends BaseModel
{
    use HasFactory;
    use HasBranch;

    protected $guarded = ['id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function menuItemVariation(): BelongsTo
    {
        return $this->belongsTo(MenuItemVariation::class);
    }

    public function modifierOptions(): BelongsToMany
    {
        return $this->belongsToMany(ModifierOption::class, 'order_item_modifier_options', 'order_item_id', 'modifier_option_id');
    }
}
