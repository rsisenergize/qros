<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('customer_lat', 10, 7)->nullable();
            $table->decimal('customer_lng', 10, 7)->nullable();
            $table->boolean('is_within_radius')->default(false);
            $table->timestamp('delivery_started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('estimated_eta_min')->nullable();
            $table->integer('estimated_eta_max')->nullable();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('map_api_key')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_fee',
                'customer_lat',
                'customer_lng',
                'is_within_radius',
                'delivery_started_at',
                'delivered_at',
                'estimated_eta_min',
                'estimated_eta_max'
            ]);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng']);
        });
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('map_api_key');
        });
    }
};
