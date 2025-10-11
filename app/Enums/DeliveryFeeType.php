<?php

namespace App\Enums;

enum DeliveryFeeType: string
{
    case FIXED = 'fixed';
    case TIERED = 'tiered';
    case PER_DISTANCE = 'per_distance';

    public static function labels(): array
    {
        return [
            self::FIXED->value => 'Fixed Rate',
            self::TIERED->value => 'Distance Tiers',
            self::PER_DISTANCE->value => 'Per Distance Rate'
        ];
    }
}
