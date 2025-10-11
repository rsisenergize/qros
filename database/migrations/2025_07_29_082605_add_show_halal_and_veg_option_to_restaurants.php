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
            $table->boolean('show_veg')->default(true)->after('allow_dine_in_orders');
            $table->boolean('show_halal')->default(false)->after('show_veg');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('show_veg');
            $table->dropColumn('show_halal');
        });
    }

};
