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
        Schema::table('recipes', function (Blueprint $table) {
            $table->unsignedBigInteger('modifier_option_id')->nullable()->after('menu_item_variation_id');
            $table->foreign('modifier_option_id')->references('id')->on('modifier_options')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropForeign(['modifier_option_id']);
            $table->dropColumn('modifier_option_id');
        });
    }
}; 