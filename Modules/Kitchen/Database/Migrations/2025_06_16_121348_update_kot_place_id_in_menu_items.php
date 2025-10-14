<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('menu_items')
            ->whereNull('kot_place_id')
            ->update(['kot_place_id' => 1]);

        // Artisan::call('kitchen:activate');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
