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
        Schema::create('order_number_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('enable_feature')->default(false);
            $table->string('prefix')->default('ORD');
            $table->unsignedTinyInteger('digits')->default(3);
            $table->string('separator')->default('-');
            $table->boolean('include_date')->default(false);
            $table->boolean('show_year')->default(false);
            $table->boolean('show_month')->default(false);
            $table->boolean('show_day')->default(false);
            $table->boolean('show_time')->default(false);
            $table->boolean('reset_daily')->default(false);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('formatted_order_number')->nullable()->after('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_number_settings');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('formatted_order_number');
        });
    }
};
