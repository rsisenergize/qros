<?php

namespace Modules\Kitchen\Database\Seeders;

use App\Livewire\Kot\Kots;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Kot;
use App\Models\KotItem;
use App\Models\MenuItem;
use Faker\Factory as Faker;

class KotSeeder extends Seeder
{

    public function run($branch): void
    {
        $kots = Kot::where('branch_id', $branch->id)->get();
        $groupedItems = [];

        foreach ($kots as $kot) {
            $groupedItems = [];
            $items = KotItem::where('kot_id', $kot->id)->with('menuItem')->get();

            foreach ($items as $key => $item) {
                $kotPlaceId = $item->menuItem->kot_place_id ?? null;
                if ($kotPlaceId === null) continue;
                $groupedItems[$kotPlaceId][] = [
                'key' => $key,
                'menu_item_id' => $item->id,
                'variation_id' => $item->variation_id ?? null,
                'quantity' => $item->quantity ?? 1,
                ];
            }

            // Assign the first kotPlaceId to the current kot and update its items
            $kotPlaceIds = array_keys($groupedItems);

            if (!empty($kotPlaceIds))
                 {
                // Assign the first kotPlaceId to the current kot
                $firstKotPlaceId = array_shift($kotPlaceIds);
                $kot->kitchen_place_id = $firstKotPlaceId;

                $kot->save();

                // Update kot_items for the first kotPlaceId to use the current kot's id
                foreach ($groupedItems[$firstKotPlaceId] as $item) {
                    KotItem::where('id', $item['menu_item_id'])->update([
                        'kot_id' => $kot->id,
                    ]);
                }

                // For the remaining kotPlaceIds, create new kots and update their items
                foreach ($kotPlaceIds as $kotPlaceId) {
                    $newKot = Kot::create([
                        'kot_number' => Kot::generateKotNumber($branch) + 1,
                        'order_id' => $kot->order_id,
                        'branch_id' => $branch->id,
                        'kitchen_place_id' => $kotPlaceId,
                        'note' => $kot->note,
                    ]);

                    foreach ($groupedItems[$kotPlaceId] as $item) {
                        KotItem::where('id', $item['menu_item_id'])->update([
                            'kot_id' => $newKot->id,
                        ]);
                    }
                }
            }
        }

    }

}
