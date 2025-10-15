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
        if (!Schema::hasColumn('packages', 'sms_count')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->integer('sms_count')->default(0);
                $table->boolean('carry_forward_sms')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('sms_count');
            $table->dropColumn('carry_forward_sms');
        });
    }
};
