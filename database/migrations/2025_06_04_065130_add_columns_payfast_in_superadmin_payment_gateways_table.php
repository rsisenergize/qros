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
            $table->string('payfast_merchant_id')->nullable();
            $table->string('payfast_merchant_key')->nullable();
            $table->string('payfast_passphrase')->nullable();
            $table->string('test_payfast_merchant_id')->nullable();
            $table->string('test_payfast_merchant_key')->nullable();
            $table->string('test_payfast_passphrase')->nullable();
            $table->enum('payfast_mode', ['sandbox', 'live'])->default('sandbox');
            $table->boolean('payfast_status')->default(false);

        });

        Schema::table('global_subscriptions', function (Blueprint $table) {
            $table->string('payfast_plan')->nullable();
            $table->string('payfast_status')->nullable();
        });
        
        Schema::table('global_invoices', function (Blueprint $table) {
            $table->string('m_payment_id')->nullable();
            $table->string('pf_payment_id')->nullable();
            $table->string('payfast_plan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->dropColumn('payfast_merchant_id');
            $table->dropColumn('payfast_merchant_key');
            $table->dropColumn('payfast_passphrase');
            $table->dropColumn('test_payfast_merchant_id');
            $table->dropColumn('test_payfast_merchant_key');
            $table->dropColumn('test_payfast_passphrase');
            $table->dropColumn('payfast_mode');
            $table->dropColumn('payfast_status');
        });

        Schema::table('global_subscriptions', function (Blueprint $table) {
            $table->dropColumn('payfast_plan');
            $table->dropColumn('payfast_status');
        });

        Schema::table('global_invoices', function (Blueprint $table) {
            $table->dropColumn('m_payment_id');
            $table->dropColumn('pf_payment_id');
            $table->dropColumn('payfast_plan');
        });
    }
};
