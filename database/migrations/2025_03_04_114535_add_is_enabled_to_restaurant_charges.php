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
        if (!Schema::hasColumn('restaurant_charges', 'is_enabled')) {
            Schema::table('restaurant_charges', function (Blueprint $table) {
                $table->boolean('is_enabled')->default(true)->after('order_types');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_charges', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
    }

};
