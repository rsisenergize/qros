<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add sort_order column
        Schema::table('menus', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('menu_name');
        });

        Schema::table('item_categories', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('category_name');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0);
        });

        // Initialize existing records with sort_order = id
        DB::table('menus')->update(['sort_order' => DB::raw('id')]);
        DB::table('item_categories')->update(['sort_order' => DB::raw('id')]);
        DB::table('menu_items')->update(['sort_order' => DB::raw('id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
