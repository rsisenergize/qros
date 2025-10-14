<?php

namespace Modules\Kitchen\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Kitchen\Entities\KitchenPlace;

use Illuminate\Support\Str;
use Carbon\Carbon;

class KitchenPlaceSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($branch): void
    {


        DB::table('kot_places')->insert([
            [
                'printer_id' => null,
                'branch_id' => $branch->id ?? null,
                'name' => 'Veg Kitchen',
                'type' => 'thermal',
                'is_default' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'printer_id' => null,
                'branch_id' => $branch->id ?? null,
                'name' => 'Non-Veg Kitchen',
                'type' => 'thermal',
                'is_default' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $kotPlaces = DB::table('kot_places')->where('is_default', false)->get();

        $menuItems = DB::table('menu_items')->get();

        // Get the two kot_place IDs
        $kotPlaceIds = $kotPlaces->pluck('id')->values();

        foreach ($menuItems as $item) {
            // Assign based on item type
            if ($item->type === 'veg') {
                // Assign to Veg Kitchen (first kot place)
                $kotPlaceId = $kotPlaceIds[0];
            } elseif ($item->type === 'non-veg') {
                // Assign to Non-Veg Kitchen (second kot place)
                $kotPlaceId = $kotPlaceIds[1];
            } else {
                // For egg items, assign to Veg Kitchen as default
                $kotPlaceId = $kotPlaceIds[0];
            }

            DB::table('menu_items')->where('id', $item->id)->limit(1)->update([
                'kot_place_id' => $kotPlaceId,
            ]);
        }
    }
}
