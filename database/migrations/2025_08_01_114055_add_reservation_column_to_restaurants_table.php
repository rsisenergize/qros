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
            $table->boolean('enable_admin_reservation')->default(true);
            $table->boolean('enable_customer_reservation')->default(true);
            $table->integer('minimum_party_size')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['enable_admin_reservation', 'enable_customer_reservation', 'minimum_party_size']);
        });
    }
};
