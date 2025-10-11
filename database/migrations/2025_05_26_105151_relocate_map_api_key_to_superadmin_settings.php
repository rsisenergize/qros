<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('map_api_key');
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('google_map_api_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('google_map_api_key');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('map_api_key')->nullable();
        });
    }
};
