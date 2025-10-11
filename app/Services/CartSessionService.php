<?php

namespace App\Services;

use App\Models\CartSession;
use App\Models\CartItem;
use App\Models\CartItemModifierOption;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\ModifierOption;
use App\Models\Tax;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class CartSessionService
{
    /**
     * Get or create a cart session for the current session.
     */
    public function getOrCreateCartSession(int $branchId, string $orderType = 'dine_in', string $placedVia = 'kiosk'): CartSession
    {
        $sessionId = Session::getId();

        $cartSession = CartSession::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('order_id', null) // Only get active cart sessions (not yet ordered)
            ->first();

        if (!$cartSession) {
            $branch = Branch::with('restaurant')->find($branchId);
            $taxMode = $branch->restaurant->tax_mode ?? 'order';

            $cartSession = CartSession::create([
                'session_id' => $sessionId,
                'branch_id' => $branchId,
                'order_type' => $orderType,
                'placed_via' => $placedVia,
                'sub_total' => 0,
                'total' => 0,
                'total_tax_amount' => 0,
                'tax_mode' => $taxMode,
            ]);
        }

        return $cartSession;
    }

    /**
     * Add an item to the cart session.
     */
    public function addItemToCart(
        CartSession $cartSession,
        int $menuItemId,
        int $quantity = 1,
        ?int $variationId = null,
        array $modifierOptionIds = []
    ): CartItem {
        $menuItem = MenuItem::findOrFail($menuItemId);
        $variation = $variationId ? MenuItemVariation::findOrFail($variationId) : null;

        // Calculate base price
        $basePrice = $variation ? $variation->price : $menuItem->price;

        // Calculate modifier price
        $modifierPrice = 0;
        if (!empty($modifierOptionIds)) {
            $modifierOptions = ModifierOption::whereIn('id', $modifierOptionIds)->get();
            $modifierPrice = $modifierOptions->sum('price');
        }

        $itemPrice = $basePrice + $modifierPrice;
        $amount = $itemPrice * $quantity;

        // Check if similar item already exists in cart
        $existingCartItem = $this->findSimilarCartItem($cartSession, $menuItemId, $variationId, $modifierOptionIds);

        // Calculate tax information
        $taxData = $this->calculateItemTax($menuItem, $itemPrice, $quantity, $cartSession->tax_mode);

        if ($existingCartItem) {
            // Update existing item
            $newQuantity = $existingCartItem->quantity + $quantity;
            $newAmount = $newQuantity * $itemPrice;
            $newTaxData = $this->calculateItemTax($menuItem, $itemPrice, $newQuantity, $cartSession->tax_mode);

            $existingCartItem->update([
                'quantity' => $newQuantity,
                'amount' => $newAmount,
                'tax_amount' => $newTaxData['tax_amount'],
                'tax_percentage' => $newTaxData['tax_percentage'],
                'tax_breakup' => $newTaxData['tax_breakup'],
            ]);
            $cartItem = $existingCartItem;
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'cart_session_id' => $cartSession->id,
                'branch_id' => $cartSession->branch_id,
                'menu_item_id' => $menuItemId,
                'menu_item_variation_id' => $variationId,
                'quantity' => $quantity,
                'price' => $itemPrice,
                'amount' => $amount,
                'tax_amount' => $taxData['tax_amount'],
                'tax_percentage' => $taxData['tax_percentage'],
                'tax_breakup' => $taxData['tax_breakup'],
            ]);

            // Add modifier options
            if (!empty($modifierOptionIds)) {
                foreach ($modifierOptionIds as $modifierOptionId) {
                    CartItemModifierOption::create([
                        'cart_item_id' => $cartItem->id,
                        'modifier_option_id' => $modifierOptionId,
                    ]);
                }
            }
        }

        $this->updateCartTotals($cartSession);

        return $cartItem;
    }

    /**
     * Update item quantity in cart.
     */
    public function updateItemQuantity(CartItem $cartItem, int $quantity): ?CartItem
    {
        if ($quantity <= 0) {
            $this->removeItemFromCart($cartItem);
            return null;
        }

        // Recalculate tax information for the updated quantity
        $menuItem = $cartItem->menuItem;
        $taxData = $this->calculateItemTax($menuItem, $cartItem->price, $quantity, $cartItem->cartSession->tax_mode);

        $cartItem->update([
            'quantity' => $quantity,
            'amount' => $cartItem->price * $quantity,
            'tax_amount' => $taxData['tax_amount'],
            'tax_percentage' => $taxData['tax_percentage'],
            'tax_breakup' => $taxData['tax_breakup'],
        ]);

        $this->updateCartTotals($cartItem->cartSession);

        return $cartItem;
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItemFromCart(CartItem $cartItem): bool
    {
        $cartSession = $cartItem->cartSession;

        // Delete modifier options first
        $cartItem->modifierOptions()->delete();

        // Delete cart item
        $cartItem->delete();

        $this->updateCartTotals($cartSession);

        return true;
    }

    /**
     * Clear all items from the cart session.
     */
    public function clearCart(CartSession $cartSession): bool
    {
        // Delete all modifier options for all cart items
        $cartItemIds = $cartSession->cartItems()->pluck('id');
        CartItemModifierOption::whereIn('cart_item_id', $cartItemIds)->delete();

        // Delete all cart items
        $cartSession->cartItems()->delete();

        // Update totals
        $cartSession->update([
            'sub_total' => 0,
            'total' => 0,
            'total_tax_amount' => 0,
        ]);

        return true;
    }

    /**
     * Get cart session with all items and relations.
     */
    public function getCartWithItems(CartSession $cartSession): CartSession
    {
        return $cartSession->load([
            'cartItems.menuItem',
            'cartItems.menuItemVariation',
            'cartItems.modifiers',
            'branch'
        ]);
    }

    /**
     * Mark cart session as ordered.
     */
    public function markAsOrdered(CartSession $cartSession, int $orderId): CartSession
    {
        $cartSession->update(['order_id' => $orderId]);
        return $cartSession;
    }

    /**
     * Find similar cart item (same item, variation, and modifiers).
     */
    private function findSimilarCartItem(
        CartSession $cartSession,
        int $menuItemId,
        ?int $variationId,
        array $modifierOptionIds
    ): ?CartItem {
        $cartItems = $cartSession->cartItems()
            ->where('menu_item_id', $menuItemId)
            ->where('menu_item_variation_id', $variationId)
            ->with('modifiers')
            ->get();

        foreach ($cartItems as $cartItem) {
            $existingModifierIds = $cartItem->modifiers->pluck('id')->sort()->values()->toArray();
            $newModifierIds = collect($modifierOptionIds)->sort()->values()->toArray();

            if ($existingModifierIds === $newModifierIds) {
                return $cartItem;
            }
        }

        return null;
    }

    /**
     * Update cart session totals.
     */
    private function updateCartTotals(CartSession $cartSession): void
    {
        $cartSession->load('cartItems', 'branch.restaurant');

        $subTotal = 0;
        $totalTaxAmount = 0;
        $total = 0;

        if ($cartSession->tax_mode === 'item') {
            // Item-level taxation
            $isInclusive = $cartSession->branch->restaurant->tax_inclusive ?? false;

            foreach ($cartSession->cartItems as $item) {
                if ($isInclusive) {
                    // For inclusive tax: subtotal = item amount - tax amount
                    $subTotal += ($item->amount - ($item->tax_amount ?? 0));
                } else {
                    // For exclusive tax: subtotal = item amount (tax will be added later)
                    $subTotal += $item->amount;
                }
                $totalTaxAmount += $item->tax_amount ?? 0;
            }

            $total = $subTotal + ($isInclusive ? 0 : $totalTaxAmount);
        } else {
            // Order-level taxation
            $subTotal = $cartSession->cartItems->sum('amount');

            // Get restaurant taxes
            $taxes = Tax::withoutGlobalScopes()
                ->where('restaurant_id', $cartSession->branch->restaurant->id)
                ->get();

            foreach ($taxes as $tax) {
                $taxAmount = ($tax->tax_percent / 100) * $subTotal;
                $totalTaxAmount += $taxAmount;
            }

            $total = $subTotal + $totalTaxAmount;
        }

        $cartSession->update([
            'sub_total' => $subTotal,
            'total' => $total,
            'total_tax_amount' => $totalTaxAmount,
        ]);
    }

    /**
     * Get current cart session for the current session.
     */
    public function getCurrentCartSession(int $branchId): ?CartSession
    {
        $sessionId = Session::getId();

        return CartSession::where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('order_id', null)
            ->first();
    }

    /**
     * Get cart item count for current session.
     */
    public function getCartItemCount(int $branchId): int
    {
        $cartSession = $this->getCurrentCartSession($branchId);

        if (!$cartSession) {
            return 0;
        }

        return $cartSession->cartItems->count();
    }

    /**
     * Get cart total for current session.
     */
    public function getCartTotal(int $branchId): float
    {
        $cartSession = $this->getCurrentCartSession($branchId);

        if (!$cartSession) {
            return 0;
        }

        return $cartSession->total;
    }

    /**
     * Calculate tax for a menu item.
     */
    private function calculateItemTax(MenuItem $menuItem, float $itemPrice, int $quantity, string $taxMode): array
    {
        if ($taxMode !== 'item') {
            return [
                'tax_amount' => null,
                'tax_percentage' => null,
                'tax_breakup' => null,
            ];
        }

        $menuItem->load('taxes');
        $taxes = $menuItem->taxes;

        if ($taxes->isEmpty()) {
            return [
                'tax_amount' => 0,
                'tax_percentage' => 0,
                'tax_breakup' => [],
            ];
        }

        $branch = Branch::with('restaurant')->find($menuItem->branch_id);
        $isInclusive = $branch->restaurant->tax_inclusive ?? false;

        $taxResult = MenuItem::calculateItemTaxes($itemPrice, $taxes, $isInclusive);

        return [
            'tax_amount' => $taxResult['tax_amount'] * $quantity,
            'tax_percentage' => $taxResult['tax_percentage'],
            'tax_breakup' => $taxResult['tax_breakdown'],
        ];
    }

    /**
     * Get tax breakdown for the cart session.
     */
    public function getTaxBreakdown(CartSession $cartSession): array
    {
        $cartSession->load('cartItems.menuItem.taxes', 'branch.restaurant');

        $taxBreakdown = [];

        if ($cartSession->tax_mode === 'order') {
            // Order-level taxation
            $taxes = Tax::withoutGlobalScopes()
                ->where('restaurant_id', $cartSession->branch->restaurant->id)
                ->get();

            foreach ($taxes as $tax) {
                $taxAmount = ($tax->tax_percent / 100) * $cartSession->sub_total;
                $taxBreakdown[$tax->tax_name] = [
                    'percent' => $tax->tax_percent,
                    'amount' => round($taxAmount, 2)
                ];
            }
        } else {
            // Item-level taxation - aggregate from items
            foreach ($cartSession->cartItems as $item) {
                if ($item->tax_breakup) {
                    foreach ($item->tax_breakup as $taxName => $taxInfo) {
                        if (!isset($taxBreakdown[$taxName])) {
                            $taxBreakdown[$taxName] = [
                                'percent' => $taxInfo['percent'],
                                'amount' => 0
                            ];
                        }
                        $taxBreakdown[$taxName]['amount'] += $taxInfo['amount'];
                    }
                }
            }
        }

        return $taxBreakdown;
    }

    /**
     * Get display price for an item (base price without tax for inclusive items).
     */
    public function getItemDisplayPrice(CartItem $cartItem): float
    {
        if ($cartItem->cartSession->tax_mode === 'item' && $cartItem->tax_amount > 0) {
            $cartSession = $cartItem->cartSession;
            $isInclusive = $cartSession->branch->restaurant->tax_inclusive ?? false;

            if ($isInclusive) {
                // For inclusive tax: display price = item price - tax amount per unit
                return $cartItem->price - ($cartItem->tax_amount / $cartItem->quantity);
            }
        }

        return $cartItem->price;
    }
}
