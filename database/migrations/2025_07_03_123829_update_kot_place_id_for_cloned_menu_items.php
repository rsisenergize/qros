<?php

use App\Models\MenuItem;
use App\Models\KotPlace;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * Usage: You must set $sourceBranchId and $newBranchId before running this migration.
     */
    public function up(): void
    {
        // Set these IDs before running the migratio

          $menuItems = MenuItem::withoutGlobalScopes()->get();

        foreach ($menuItems as $item) {
            // Only update kot_place_id if it is null
            if (is_null($item->kot_place_id)) {
                // Save the kot_place_id of the current branch for this menu item
                $kotPlace = KotPlace::withoutGlobalScopes()
                    ->where('branch_id', $item->branch_id)
                    ->first();

                if ($kotPlace) {
                    $item->kot_place_id = $kotPlace->id;
                    $item->save();
                }
            }
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback logic
    }

};
