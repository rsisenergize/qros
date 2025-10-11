<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItemModifierOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_item_id',
        'modifier_option_id',
    ];

    /**
     * Get the cart item that owns the modifier option.
     */
    public function cartItem(): BelongsTo
    {
        return $this->belongsTo(CartItem::class);
    }

    /**
     * Get the modifier option that owns the cart item modifier option.
     */
    public function modifierOption(): BelongsTo
    {
        return $this->belongsTo(ModifierOption::class);
    }
}

