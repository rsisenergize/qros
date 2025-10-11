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
            // Rename Payfast columns to follow standard naming convention
            $table->renameColumn('payfast_merchant_id', 'live_payfast_merchant_id');
            $table->renameColumn('payfast_merchant_key', 'live_payfast_merchant_key');
            $table->renameColumn('payfast_passphrase', 'live_payfast_passphrase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            // Revert Payfast column names back to original
            $table->renameColumn('live_payfast_merchant_id', 'payfast_merchant_id');
            $table->renameColumn('live_payfast_merchant_key', 'payfast_merchant_key');
            $table->renameColumn('live_payfast_passphrase', 'payfast_passphrase');
        });
    }
};
