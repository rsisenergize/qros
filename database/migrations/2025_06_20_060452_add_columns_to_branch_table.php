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

        Schema::table('branches', function (Blueprint $table) {
            $table->string('cloned_branch_name')->nullable()->after('name');
            $table->string('cloned_branch_id')->nullable()->after('cloned_branch_name');
            $table->boolean('is_menu_clone')->default(false)->after('cloned_branch_id');
            $table->boolean('is_item_categories_clone')->default(false)->after('is_menu_clone');
            $table->boolean('is_menu_items_clone')->default(false)->after('is_item_categories_clone');
            $table->boolean('is_item_modifiers_clone')->default(false)->after('is_menu_items_clone');
            $table->boolean('is_modifiers_groups_clone')->default(false)->after('is_item_modifiers_clone');
            $table->boolean('is_clone_reservation_settings')->default(false)->after('is_item_modifiers_clone');
            $table->boolean('is_clone_delivery_settings')->default(false)->after('is_clone_reservation_settings');
            $table->boolean('is_clone_kot_setting')->default(false)->after('is_clone_delivery_settings');
        });

    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'cloned_branch_name',
                'cloned_branch_id',
                'is_menu_clone',
                'is_item_categories_clone',
                'is_menu_items_clone',
                'is_item_modifiers_clone',
                'is_modifiers_groups_clone',
                'is_clone_reservation_settings',
                'is_clone_delivery_settings',
                'is_clone_kot_setting'
            ]);
        });
    }

};
