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
        // Add reference_id to restaurant_payments table
        if (!Schema::hasColumn('restaurant_payments', 'reference_id')) {
            Schema::table('restaurant_payments', function (Blueprint $table) {
                $table->string('reference_id')->nullable()->after('transaction_id');
            });
        }

        // Add reference_id to global_invoices table
        if (!Schema::hasColumn('global_invoices', 'reference_id')) {
            Schema::table('global_invoices', function (Blueprint $table) {
                $table->string('reference_id')->nullable()->after('transaction_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_payments', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_payments', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
        });

        Schema::table('global_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('global_invoices', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
        });
    }
};

