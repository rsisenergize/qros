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
        Schema::table('custom_menus', function (Blueprint $table) {
            $table->enum('position', ['header', 'footer'])->default('header')->after('is_active');
            $table->integer('sort_order')->default(0)->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_menus', function (Blueprint $table) {
            $table->dropColumn(['position', 'sort_order']);
        });
    }
};
