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
            $table->boolean('hide_menu_item_image_on_pos')->default(false)->after('show_order_type_options');
            $table->boolean('hide_menu_item_image_on_customer_site')->default(false)->after('hide_menu_item_image_on_pos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['hide_menu_item_image_on_pos', 'hide_menu_item_image_on_customer_site']);
        });
    }
};
