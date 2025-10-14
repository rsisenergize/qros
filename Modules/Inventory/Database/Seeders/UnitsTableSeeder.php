<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Unit;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {

        foreach (Unit::UNITS as $unit) {
            Unit::firstOrCreate(array_merge($unit, [
                'branch_id' => 1 // Assuming branch_id 1 exists
            ]));
        }
    }
}
