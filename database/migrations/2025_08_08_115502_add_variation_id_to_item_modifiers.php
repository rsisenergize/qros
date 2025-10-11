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
        Schema::table('item_modifiers', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_variation_id')->nullable()->after('menu_item_id');
            $table->foreign('menu_item_variation_id')->references('id')->on('menu_item_variations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_modifiers', function (Blueprint $table) {
            $table->dropForeign(['menu_item_variation_id']);
            $table->dropColumn('menu_item_variation_id');
        });
    }
};
