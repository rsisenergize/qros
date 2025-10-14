<?php

namespace Modules\Kitchen\Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Modules\Kitchen\Database\Seeders\KitchenPlaceSeeder;

class KitchenDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::with('branches')->get();

        foreach ($restaurants as $restaurant) {
            $this->command->info('Seeding restaurant: ' . ($restaurant->id));

            $branch = $restaurant->branches->first();
            // $this->command->info('brannch id'. $branch->id);

            if (!app()->environment('codecanyon')) {
                $this->call(KitchenPlaceSeeder::class, false, ['branch' => $branch]);
                $this->call(KotSeeder::class, false, ['branch' => $branch]);
            }
        }
    }
}
