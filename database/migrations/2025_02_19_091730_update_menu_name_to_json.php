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
    public function up()
    {
        // Step 1: Add the new JSON column
        // Check if the column exists and is not already JSON type
        if (
            Schema::hasColumn('menus', 'menu_name') &&
            DB::connection()->getSchemaBuilder()->getColumnType('menus', 'menu_name') !== 'json'
        ) {

            Schema::table('menus', function (Blueprint $table) {
                $table->text('menu_name_json')->nullable()->after('menu_name');
            });

            // Step 2: Copy existing values into JSON format
            DB::statement('UPDATE menus SET menu_name_json = JSON_OBJECT("en", menu_name)');

            // Step 3: Drop the old column and rename the new one
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('menu_name');
            });

            Schema::table('menus', function (Blueprint $table) {
                $table->renameColumn('menu_name_json', 'menu_name');
            });
        }

        // Step 1: Add the new JSON column
        // Check if the column exists and is not already JSON type
        if (
            Schema::hasColumn('item_categories', 'category_name') &&
            DB::connection()->getSchemaBuilder()->getColumnType('item_categories', 'category_name') !== 'json'
        ) {
            Schema::table('item_categories', function (Blueprint $table) {
                $table->text('category_name_json')->nullable()->after('category_name');
            });

            // Step 2: Copy existing values into JSON format
            DB::statement('UPDATE item_categories SET category_name_json = JSON_OBJECT("en", category_name)');

            // Step 3: Drop the old column and rename the new one
            Schema::table('item_categories', function (Blueprint $table) {
                $table->dropColumn('category_name');
            });

            Schema::table('item_categories', function (Blueprint $table) {
                $table->renameColumn('category_name_json', 'category_name');
            });
        }



        Schema::create('menu_item_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->unique(['menu_item_id', 'locale']);
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('item_name');
            $table->text('description')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('menu_item_translations');
    }
};
