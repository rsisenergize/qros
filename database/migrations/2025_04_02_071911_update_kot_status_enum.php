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
        Schema::table('kots', function (Blueprint $table) {
            $table->enum('status', ['pending_confirmation', 'in_kitchen', 'food_ready', 'served'])
                ->default('in_kitchen')
                ->change();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('auto_confirm_orders')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kots', function (Blueprint $table) {
            $table->enum('status', ['in_kitchen', 'food_ready', 'served'])
                ->default('in_kitchen')
                ->change();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('auto_confirm_orders');
        });
    }
};
