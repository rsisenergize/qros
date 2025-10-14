<?php

namespace Modules\Kiosk\Services;

use App\Services\CartSessionService;
use App\Models\CartSession;
use App\Models\CartItem;
use Illuminate\Support\Facades\Session;

class KioskCartService extends CartSessionService
{
    /**
     * Initialize kiosk cart session.
     */
    public function initializeKioskCart(int $branchId, string $orderType = 'dine_in'): CartSession
    {
        return $this->getOrCreateCartSession($branchId, $orderType, 'kiosk');
    }

    /**
     * Add item to kiosk cart with customizations.
     */
    public function addKioskItem(
        int $branchId,
        int $menuItemId,
        int $quantity = 1,
        ?int $variationId = null,
        array $modifierOptionIds = [],
        string $orderType = 'dine_in'
    ): array {
        $cartSession = $this->initializeKioskCart($branchId, $orderType);
        
        $cartItem = $this->addItemToCart(
            $cartSession,
            $menuItemId,
            $quantity,
            $variationId,
            $modifierOptionIds
        );

        return [
            'success' => true,
            'cart_item' => $cartItem,
            'cart_session' => $this->getCartWithItems($cartSession),
            'cart_count' => $cartSession->total_quantity,
            'cart_total' => $cartSession->total,
        ];
    }

    /**
     * Get kiosk cart summary.
     */
    public function getKioskCartSummary(int $branchId): array
    {
        $cartSession = $this->getCurrentCartSession($branchId);
        
        if (!$cartSession) {
            return [
                'items' => [],
                'count' => 0,
                'sub_total' => 0,
                'total' => 0,
                'total_tax_amount' => 0,
                'tax_mode' => 'order',
                'tax_breakdown' => [],
                'is_empty' => true,
            ];
        }

        $cartWithItems = $this->getCartWithItems($cartSession);
        
        return [
            'items' => $cartWithItems->cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'menu_item' => [
                        'id' => $item->menuItem->id,
                        'name' => $item->menuItem->item_name,
                        'image_url' => $item->menuItem->item_photo_url,
                    ],
                    'variation' => $item->menuItemVariation ? [
                        'id' => $item->menuItemVariation->id,
                        'name' => $item->menuItemVariation->variation,
                        'price' => $item->menuItemVariation->price,
                    ] : null,
                    'modifiers' => $item->modifiers->map(function ($modifier) {
                        return [
                            'id' => $modifier->id,
                            'name' => $modifier->name,
                            'price' => $modifier->price,
                        ];
                    }),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'amount' => $item->amount,
                    'tax_amount' => $item->tax_amount,
                    'tax_percentage' => $item->tax_percentage,
                    'tax_breakup' => $item->tax_breakup,
                    'display_price' => $this->getItemDisplayPrice($item),
                ];
            }),
            'count' => $cartWithItems->total_quantity,
            'sub_total' => $cartWithItems->sub_total,
            'total' => $cartWithItems->total,
            'total_tax_amount' => $cartWithItems->total_tax_amount,
            'tax_mode' => $cartWithItems->tax_mode,
            'tax_breakdown' => $this->getTaxBreakdown($cartWithItems),
            'is_empty' => $cartWithItems->isEmpty(),
            'order_type' => $cartSession->order_type,
        ];
    }

    /**
     * Update kiosk cart item quantity.
     */
    public function updateKioskItemQuantity(int $cartItemId, int $quantity): array
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        $this->updateItemQuantity($cartItem, $quantity + intval($cartItem->quantity));
        $success = true;
        $message = 'Item quantity updated';

        $cartSession = $cartItem->cartSession;

        
        
        return [
            'success' => $success,
            'message' => $message,
            'cart_session' => $this->getCartWithItems($cartSession),
            'cart_count' => $cartSession->total_quantity,
            'cart_total' => $cartSession->total,
        ];
    }

    /**
     * Remove item from kiosk cart.
     */
    public function removeKioskItem(int $cartItemId): array
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cartSession = $cartItem->cartSession;
        
        $this->removeItemFromCart($cartItem);
        
        return [
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_session' => $this->getCartWithItems($cartSession),
            'cart_count' => $cartSession->total_quantity,
            'cart_total' => $cartSession->total,
        ];
    }

    /**
     * Clear kiosk cart.
     */
    public function clearKioskCart(int $branchId): array
    {
        $cartSession = $this->getCurrentCartSession($branchId);
        
        if (!$cartSession) {
            return [
                'success' => false,
                'message' => 'No active cart found',
            ];
        }

        $this->clearCart($cartSession);

        session()->forget('customerInfo');
        
        return [
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => 0,
        ];
    }

    /**
     * Get cart badge count for kiosk header.
     */
    public function getKioskCartBadgeCount(int $branchId): int
    {
        return $this->getCartItemCount($branchId);
    }

    /**
     * Check if kiosk cart is empty.
     */
    public function isKioskCartEmpty(int $branchId): bool
    {
        $cartSession = $this->getCurrentCartSession($branchId);
        return !$cartSession || $cartSession->isEmpty();
    }

    /**
     * Set order type for kiosk cart.
     */
    public function setKioskOrderType(int $branchId, string $orderType): array
    {

        $cartSession = $this->getCurrentCartSession($branchId);
        
        if (!$cartSession) {
            $cartSession = $this->initializeKioskCart($branchId, $orderType);
        } else {
            $cartSession->update(['order_type' => $orderType]);
        }
        
        return [
            'success' => true,
            'message' => 'Order type updated',
            'order_type' => $orderType,
        ];
    }
}

