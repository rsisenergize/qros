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
            $table->unsignedInteger('token_number')->nullable()->after('kot_number');
            $table->unsignedBigInteger('order_type_id')->nullable()->after('order_id');
            $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kots', function (Blueprint $table) {
            $table->dropForeign(['order_type_id']);
            $table->dropColumn(['token_number', 'order_type_id']);
        });
    }
};
