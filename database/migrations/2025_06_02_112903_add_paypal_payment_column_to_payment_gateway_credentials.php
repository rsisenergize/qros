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
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->string('paypal_client_id')->nullable();
            $table->string('paypal_secret')->nullable();
            $table->boolean('paypal_status')->default(false);
            $table->enum('paypal_mode', ['sandbox', 'live'])->default('sandbox');
            $table->string('sandbox_paypal_client_id')->nullable();
            $table->string('sandbox_paypal_secret')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            //
        });
    }
};
