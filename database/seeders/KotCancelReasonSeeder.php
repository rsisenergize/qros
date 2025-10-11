<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KotCancelReason;

class KotCancelReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($restaurant = null): void
    {
        $cancelReasons = [
            // Order cancellation reasons
            [
                'reason' => 'Customer changed their mind',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],
            [
                'reason' => 'Customer requested to cancel',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],
            [
                'reason' => 'Payment issues',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],


            [
                'reason' => 'Customer no longer wants the order',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],

            [
                'reason' => 'Ingredient not available',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],

            [
                'reason' => 'Preparation time too long',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],
            [
                'reason' => 'Quality issue with ingredients',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],

            // Both order and KOT cancellation reasons
            [
                'reason' => 'System error/Technical issue',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],

            [
                'reason' => 'Restaurant closing early',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],
              [
                'reason' => 'Other',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],


        ];

        foreach ($cancelReasons as $reason) {
            $reason['restaurant_id'] = $restaurant->id ?? null;
            KotCancelReason::create($reason);
        }
    }
}
