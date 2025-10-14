<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventoryItem;

class InventoryItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Meat & Poultry (Category 1)
            [
                'name' => 'Chicken Breast',
                'inventory_item_category_id' => 1,
                'unit_id' => 1, // kg
                'threshold_quantity' => 20.00, // Alert when below 20kg
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
            [
                'name' => 'Ground Beef',
                'inventory_item_category_id' => 1,
                'unit_id' => 1, // kg
                'threshold_quantity' => 15.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],

            // Seafood (Category 2)
            [
                'name' => 'Salmon Fillet',
                'inventory_item_category_id' => 2,
                'unit_id' => 1, // kg
                'threshold_quantity' => 10.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
            [
                'name' => 'Fresh Shrimp',
                'inventory_item_category_id' => 2,
                'unit_id' => 1, // kg
                'threshold_quantity' => 8.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],

            // Dairy & Eggs (Category 3)
            [
                'name' => 'Heavy Cream',
                'inventory_item_category_id' => 3,
                'unit_id' => 3, // L
                'threshold_quantity' => 10.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
            [
                'name' => 'Fresh Eggs',
                'inventory_item_category_id' => 3,
                'unit_id' => 7, // dozen
                'threshold_quantity' => 20.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],

            // Fresh Produce (Category 4)
            [
                'name' => 'Tomatoes',
                'inventory_item_category_id' => 4,
                'unit_id' => 1, // kg
                'threshold_quantity' => 15.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
            [
                'name' => 'Lettuce',
                'inventory_item_category_id' => 4,
                'unit_id' => 5, // piece
                'threshold_quantity' => 20.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],

            // Herbs & Spices (Category 5)
            [
                'name' => 'Ground Black Pepper',
                'inventory_item_category_id' => 5,
                'unit_id' => 2, // grams
                'threshold_quantity' => 500.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
            [
                'name' => 'Fresh Basil',
                'inventory_item_category_id' => 5,
                'unit_id' => 1, // kg
                'threshold_quantity' => 1.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],

            // Dry Goods (Category 6)
            [
                'name' => 'Basmati Rice',
                'inventory_item_category_id' => 6,
                'unit_id' => 1, // kg
                'threshold_quantity' => 25.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
            [
                'name' => 'All-Purpose Flour',
                'inventory_item_category_id' => 6,
                'unit_id' => 1, // kg
                'threshold_quantity' => 20.00,
                'unit_purchase_price' => rand(10, 100),
                'preferred_supplier_id' => rand(1, 5),
                'reorder_quantity' => rand(10, 20),
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create(array_merge($item, [
                'branch_id' => 1
            ]));
        }
    }
}