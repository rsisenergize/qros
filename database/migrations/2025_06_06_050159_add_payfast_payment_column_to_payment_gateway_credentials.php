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
            $table->string('payfast_merchant_id')->nullable();
            $table->string('payfast_merchant_key')->nullable();
            $table->string('payfast_passphrase')->nullable();
            $table->enum('payfast_mode', ['sandbox', 'live'])->default('sandbox');
            $table->boolean('payfast_status')->default(false);
            $table->string('test_payfast_merchant_id')->nullable();
            $table->string('test_payfast_merchant_key')->nullable();
            $table->string('test_payfast_passphrase')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            
        });
    }
};
