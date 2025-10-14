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
       Schema::table('packages', function (Blueprint $table) {
                $table->string('xendit_annual_plan_id')->nullable()->after('paystack_monthly_plan_id');
                $table->string('xendit_monthly_plan_id')->nullable()->after('xendit_annual_plan_id');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            //
        });
    }
};
