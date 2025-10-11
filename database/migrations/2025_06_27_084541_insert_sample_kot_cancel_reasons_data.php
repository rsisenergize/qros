<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all restaurants
        $restaurants = DB::table('restaurants')->get();

        // Sample cancel reasons data
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
                'reason' => 'Order placed by mistake',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],
            [
                'reason' => 'Customer no longer wants the order',
                'cancel_order' => true,
                'cancel_kot' => false,
            ],


            // KOT cancellation reasons
            [
                'reason' => 'Ingredient not available',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],
            [
                'reason' => 'Item out of stock',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],
            [
                'reason' => 'Kitchen overloaded',
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
            [
                'reason' => 'Equipment malfunction',
                'cancel_order' => false,
                'cancel_kot' => true,
            ],
            [
                'reason' => 'Chef unavailable',
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
                'reason' => 'Wrong item ordered',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],


            [
                'reason' => 'Staff unavailable',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],
            [
                'reason' => 'Health and safety concerns',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],
              [
                'reason' => 'Other',
                'cancel_order' => true,
                'cancel_kot' => true,
            ],


        ];

        // Insert data for each restaurant
        foreach ($restaurants as $restaurant) {
            foreach ($cancelReasons as $reason) {
                // Check if this reason already exists for this restaurant
                $exists = DB::table('kot_cancel_reasons')
                    ->where('restaurant_id', $restaurant->id)
                    ->where('reason', $reason['reason'])
                    ->exists();

                if (!$exists) {
                    DB::table('kot_cancel_reasons')->insert([
                        'restaurant_id' => $restaurant->id,
                        'reason' => $reason['reason'],
                        'cancel_order' => $reason['cancel_order'],
                        'cancel_kot' => $reason['cancel_kot'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define the sample reasons to remove
        $sampleReasons = [
            'Customer changed their mind',
            'Customer requested to cancel',
            'Payment issues',
            'Wrong delivery address',
            'Order placed by mistake',
            'Customer no longer wants the order',
            'Ingredient not available',
            'Item out of stock',
            'Kitchen overloaded',
            'Preparation time too long',
            'Quality issue with ingredients',
            'Equipment malfunction',
            'Chef unavailable',
            'System error/Technical issue',
            'Wrong item ordered',
            'Staff unavailable',
            'Health and safety concerns',
            'Other',
        ];

        // Remove sample data
        DB::table('kot_cancel_reasons')
            ->whereIn('reason', $sampleReasons)
            ->delete();
    }
};
