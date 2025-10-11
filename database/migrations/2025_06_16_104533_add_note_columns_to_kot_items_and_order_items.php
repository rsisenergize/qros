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
        Schema::table('kot_items', function (Blueprint $table) {
            $table->text('note')->nullable()->after('menu_item_variation_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->text('note')->nullable()->after('menu_item_variation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kot_items', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
