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
        if (!Schema::hasColumn('superadmin_payment_gateways', 'paddle_webhook_secret')) {
            Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
                $table->text('paddle_webhook_secret')->nullable()->after('live_paddle_client_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            if (Schema::hasColumn('superadmin_payment_gateways', 'paddle_webhook_secret')) {
                $table->dropColumn('paddle_webhook_secret');
            }
        });
    }
};


