<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventoryStock;
use Carbon\Carbon;

class InventoryStockTableSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = [
            // Chicken Breast (ID: 1)
            [
                'inventory_item_id' => 1,
                'quantity' => 25.00, // kg
            ],
            [
                'inventory_item_id' => 1,
                'quantity' => 15.00, // kg
            ],

            // Ground Beef (ID: 2)
            [
                'inventory_item_id' => 2,
                'quantity' => 20.00, // kg
            ],

            // Salmon Fillet (ID: 3)
            [
                'inventory_item_id' => 3,
                'quantity' => 12.50, // kg
            ],

            // Heavy Cream (ID: 5)
            [
                'inventory_item_id' => 5,
                'quantity' => 15.00, // L
            ],

            // Fresh Eggs (ID: 6)
            [
                'inventory_item_id' => 6,
                'quantity' => 25.00, // dozens
            ],

            // Basmati Rice (ID: 11)
            [
                'inventory_item_id' => 11,
                'quantity' => 50.00, // kg
            ],
        ];

        foreach ($stocks as $stock) {
            InventoryStock::create(array_merge($stock, [
                'branch_id' => 1
            ]));
        }
    }
}
