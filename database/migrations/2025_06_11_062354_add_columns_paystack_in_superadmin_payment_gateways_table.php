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
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->string('live_paystack_key')->nullable();
            $table->string('live_paystack_secret')->nullable();
            $table->string('live_paystack_merchant_email')->nullable();
            $table->string('test_paystack_key')->nullable();
            $table->string('test_paystack_secret')->nullable();
            $table->string('test_paystack_merchant_email')->nullable();
            $table->string('paystack_payment_url')->nullable()->default('https://api.paystack.co');
            $table->boolean('paystack_status')->default(false);
            $table->enum('paystack_mode', ['sandbox', 'live'])->default('sandbox');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->dropColumn('live_paystack_key');
            $table->dropColumn('live_paystack_secret');
            $table->dropColumn('live_paystack_merchant_email');
            $table->dropColumn('test_paystack_key');
            $table->dropColumn('test_paystack_secret');
            $table->dropColumn('test_paystack_merchant_email');
            $table->dropColumn('paystack_payment_url');
            $table->dropColumn('paystack_status');
            $table->dropColumn('paystack_mode');
        });
    }
};
