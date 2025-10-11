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
            $table->string('live_paypal_client_id')->nullable();
            $table->string('live_paypal_secret')->nullable();
            $table->string('test_paypal_client_id')->nullable();
            $table->string('test_paypal_secret')->nullable();
            $table->boolean('paypal_status')->default(false);
            $table->enum('paypal_mode', ['sandbox', 'live'])->default('sandbox');
        });

        Schema::table('restaurant_payments', function (Blueprint $table) {
            $table->string('paypal_payment_id')->nullable();
        });

        Schema::table('global_invoices', function (Blueprint $table) {
            $table->string('event_id')->nullable()->after('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->dropColumn([
                'live_paypal_client_id',
                'live_paypal_secret',
                'test_paypal_client_id',
                'test_paypal_secret',
                'paypal_status',
                'paypal_mode',
            ]);
        });

        Schema::table('restaurant_payments', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_payment_id',
            ]);
        });
        
        Schema::table('global_invoices', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
};
