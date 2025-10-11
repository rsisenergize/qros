<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'branch_id',
        'order_id',
        'order_type_id',
        'placed_via',
        'order_type',
        'sub_total',
        'total',
        'total_tax_amount',
        'tax_mode',
    ];

    /**
     * Get the branch that owns the cart session.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the order that owns the cart session.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order type that owns the cart session.
     */
    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }

    /**
     * Get the cart items for the cart session.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total quantity of items in the cart.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->cartItems->sum('quantity');
    }

    /**
     * Check if the cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->cartItems->count() === 0;
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(): void
    {
        $this->cartItems()->delete();
        $this->update([
            'sub_total' => 0,
            'total' => 0,
        ]);
    }
}

