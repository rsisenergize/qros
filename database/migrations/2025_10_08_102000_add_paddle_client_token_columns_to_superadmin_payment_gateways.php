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
            if (!Schema::hasColumn('superadmin_payment_gateways', 'test_paddle_client_token')) {
                $table->text('test_paddle_client_token')->nullable()->after('test_paddle_public_key');
            }
            if (!Schema::hasColumn('superadmin_payment_gateways', 'live_paddle_client_token')) {
                $table->text('live_paddle_client_token')->nullable()->after('live_paddle_public_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            if (Schema::hasColumn('superadmin_payment_gateways', 'test_paddle_client_token')) {
                $table->dropColumn('test_paddle_client_token');
            }
            if (Schema::hasColumn('superadmin_payment_gateways', 'live_paddle_client_token')) {
                $table->dropColumn('live_paddle_client_token');
            }
        });
    }
};
