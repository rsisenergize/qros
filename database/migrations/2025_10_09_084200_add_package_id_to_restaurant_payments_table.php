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
        if (!Schema::hasColumn('restaurant_payments', 'package_id')) {
            Schema::table('restaurant_payments', function (Blueprint $table) {
                $table->unsignedBigInteger('package_id')->nullable()->after('restaurant_id');
                $table->foreign('package_id')->references('id')->on('packages')->onDelete('SET NULL');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_payments', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_payments', 'package_id')) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            }
        });
    }
};

