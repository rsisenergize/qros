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
        if (!Schema::hasColumn('packages', 'paddle_annual_price_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->string('paddle_annual_price_id')->nullable()->after('xendit_monthly_plan_id');
                $table->string('paddle_monthly_price_id')->nullable()->after('paddle_annual_price_id');
                $table->string('paddle_lifetime_price_id')->nullable()->after('paddle_monthly_price_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'paddle_annual_price_id')) {
                $table->dropColumn(['paddle_annual_price_id', 'paddle_monthly_price_id', 'paddle_lifetime_price_id']);
            }
        });
    }
};

