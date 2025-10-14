<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryItem;
use App\Models\User;
use Carbon\Carbon;

class InventoryMovementsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Get real IDs from database
        $chickenBreast = InventoryItem::where('name', 'like', '%Chicken Breast%')->first();
        $groundBeef = InventoryItem::where('name', 'like', '%Ground Beef%')->first();
        $salmon = InventoryItem::where('name', 'like', '%Salmon%')->first();
        $heavyCream = InventoryItem::where('name', 'like', '%Heavy Cream%')->first();
        $rice = InventoryItem::where('name', 'like', '%Rice%')->first();

        $user = User::whereNotNull('restaurant_id')->first(); // Or use a specific query to get the desired user

        if (!$chickenBreast || !$groundBeef || !$salmon || !$heavyCream || !$rice || !$user) {
            throw new \Exception('Required inventory items or user not found in database');
        }

        $movements = [
            // Chicken Breast Movements
            [
                'inventory_item_id' => $chickenBreast->id,
                'quantity' => 25.00,
                'transaction_type' => 'in',
                'supplier_id' => 1,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDays(2),
                'unit_purchase_price' => $chickenBreast->unit_purchase_price,
                'expiration_date' => Carbon::now()->addDays(rand(1, 15)),
            ],
            [
                'inventory_item_id' => $chickenBreast->id,
                'quantity' => 15.00,
                'transaction_type' => 'in',
                'supplier_id' => 1,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDay(),
                'unit_purchase_price' => $chickenBreast->unit_purchase_price,
                'expiration_date' => Carbon::now()->addDays(rand(1, 15)),
            ],
            [
                'inventory_item_id' => $chickenBreast->id,
                'quantity' => 8.50,
                'transaction_type' => 'out',
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subHours(12)
            ],

            // Ground Beef Movements
            [
                'inventory_item_id' => $groundBeef->id,
                'quantity' => 20.00,
                'transaction_type' => 'in',
                'supplier_id' => 2,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDays(3),
                'unit_purchase_price' => $groundBeef->unit_purchase_price,
                'expiration_date' => Carbon::now()->addDays(rand(1, 15)),
            ],
            [
                'inventory_item_id' => $groundBeef->id,
                'quantity' => 5.75,
                'transaction_type' => 'out',
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subHours(18)
            ],

            // Salmon Movements
            [
                'inventory_item_id' => $salmon->id,
                'quantity' => 15.00,
                'transaction_type' => 'in',
                'supplier_id' => 3,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDays(2),
                'unit_purchase_price' => $salmon->unit_purchase_price,
                'expiration_date' => Carbon::now()->addDays(rand(1, 15)),
            ],
            [
                'inventory_item_id' => $salmon->id,
                'quantity' => 2.50,
                'transaction_type' => 'waste',
                'waste_reason' => 'spoilage',
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subHours(24)
            ],

            // Heavy Cream Movements
            [
                'inventory_item_id' => $heavyCream->id,
                'quantity' => 20.00,
                'transaction_type' => 'in',
                'supplier_id' => 1,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDays(4),
                'unit_purchase_price' => $heavyCream->unit_purchase_price,
                'expiration_date' => Carbon::now()->addDays(rand(1, 15)),
            ],
            [
                'inventory_item_id' => $heavyCream->id,
                'quantity' => 5.00,
                'transaction_type' => 'out',
                'supplier_id' => 1,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subHours(36)
            ],

            // Rice Movements
            [
                'inventory_item_id' => $rice->id,
                'quantity' => 50.00,
                'transaction_type' => 'in',
                'supplier_id' => 2,
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDays(7),
                'unit_purchase_price' => $rice->unit_purchase_price,
                'expiration_date' => Carbon::now()->addDays(rand(1, 15)),
            ],
            [
                'inventory_item_id' => $rice->id,
                'quantity' => 12.50,
                'transaction_type' => 'out',
                'added_by' => $user->id,
                'created_at' => Carbon::now()->subDays(2)
            ],
        ];

        foreach ($movements as $movement) {
            InventoryMovement::create(array_merge($movement, [
                'branch_id' => 1
            ]));
        }
    }
}