<?php

namespace App\Enums;

enum OrderStatus: string

{
    case PLACED = 'placed';
    // case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PREPARING = 'preparing';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case OUT_FOR_DELIVERY = 'out_for_delivery'; // Order is being delivered
    case SERVED = 'served'; // Order served at table (for dine-in)
    case DELIVERED = 'delivered'; // Order delivered to the customer
    case CANCELLED = 'cancelled'; // Order cancelled

    public function label(): string
    {
        return match ($this) {
            self::PLACED => 'Order Placed',
            self::CONFIRMED => 'Order Confirmed',
            self::PREPARING => 'Order Preparing',
            self::READY_FOR_PICKUP => 'Order is Ready for Pickup',
            self::OUT_FOR_DELIVERY => 'Order is Out for Delivery',
            self::SERVED => 'Order Served',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Order Cancelled',
        };
    }

    /**
     * Check if the package type is editable.
     *
     * @return bool
     */
    // public function isEditable(): bool
    // {
    //     return !in_array($this, [self::DELIVERED], true);
    // }

    /**
     * Check if the package type is deletable.
     *
     * @return bool
     */
    // public function isDeletable(): bool
    // {
    //     return !in_array($this, [self::DELIVERED], true);
    // }
}
