<?php

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $restaurant = Restaurant::with('branches')->get();

        foreach ($restaurant as $restaurant) {

            foreach ($restaurant->branches as $branch) {
                $kotPlace = $branch->kotPlaces()->first();

                if ($kotPlace) {
                    MenuItem::whereNull('kot_place_id')
                        ->update(['kot_place_id' => $kotPlace->id]);
                }

            }

            
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }

};
