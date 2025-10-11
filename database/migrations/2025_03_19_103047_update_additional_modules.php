<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $packages = DB::table('packages')->get();

        foreach ($packages as $package) {
            $additionalFeatures = json_decode($package->additional_features, true) ?? [];

            if (($key = array_search('Import Report', $additionalFeatures)) !== false) {
                unset($additionalFeatures[$key]);

                DB::table('packages')
                    ->where('id', $package->id)
                    ->update([
                        'additional_features' => json_encode(array_values($additionalFeatures))
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

};
