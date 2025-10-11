<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_session_id',
        'branch_id',
        'menu_item_id',
        'menu_item_variation_id',
        'quantity',
        'price',
        'amount',
        'tax_amount',
        'tax_percentage',
        'tax_breakup',
    ];


    /**
     * Get the cart session that owns the cart item.
     */
    public function cartSession(): BelongsTo
    {
        return $this->belongsTo(CartSession::class);
    }

    /**
     * Get the branch that owns the cart item.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the menu item that owns the cart item.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get the menu item variation that owns the cart item.
     */
    public function menuItemVariation(): BelongsTo
    {
        return $this->belongsTo(MenuItemVariation::class);
    }

    /**
     * Get the modifier options for the cart item.
     */
    public function modifierOptions(): HasMany
    {
        return $this->hasMany(CartItemModifierOption::class);
    }

    /**
     * Get the modifier options with their details.
     */
    public function modifiers()
    {
        return $this->belongsToMany(ModifierOption::class, 'cart_item_modifier_options');
    }

    /**
     * Calculate the total price including modifiers.
     */
    public function calculateTotalPrice(): float
    {
        $basePrice = $this->menuItemVariation ? $this->menuItemVariation->price : $this->menuItem->price;
        $modifierPrice = $this->modifiers->sum('price');
        
        return ($basePrice + $modifierPrice) * $this->quantity;
    }

    /**
     * Update the amount based on current price and quantity.
     */
    public function updateAmount(): void
    {
        $this->update(['amount' => $this->calculateTotalPrice()]);
    }
}

