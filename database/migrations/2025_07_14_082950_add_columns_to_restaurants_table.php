<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('restaurants', function (Blueprint $table) {
            $table->string('customer_site_language')->nullable();
        });
        // Set default language 'en' for all existing restaurants
        DB::table('restaurants')->update(['customer_site_language' => 'en']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('customer_site_language');
        });
    }
};
