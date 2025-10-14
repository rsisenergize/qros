<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Supplier;

class SuppliersTableSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Fresh Produce Co.',
                'phone' => '555-0101',
                'email' => 'orders@freshproduce.com',
                'address' => '123 Farmer\'s Market Lane'
            ],
            [
                'name' => 'Premium Meats Inc.',
                'phone' => '555-0102',
                'email' => 'sales@premiummeats.com',
                'address' => '456 Butcher Street'
            ],
            [
                'name' => 'Seafood Express',
                'phone' => '555-0103',
                'email' => 'orders@seafoodexpress.com',
                'address' => '789 Harbor Road'
            ],
            [
                'name' => 'Global Foods Ltd.',
                'phone' => '555-0104',
                'email' => 'wholesale@globalfoods.com',
                'address' => '321 Import Drive'
            ],
            [
                'name' => 'Restaurant Supply Co.',
                'phone' => '555-0105',
                'email' => 'sales@restaurantsupply.com',
                'address' => '654 Kitchen Avenue'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create(array_merge($supplier, [
                'restaurant_id' => 1 // Assuming restaurant_id 1 exists
            ]));
        }
    }
}