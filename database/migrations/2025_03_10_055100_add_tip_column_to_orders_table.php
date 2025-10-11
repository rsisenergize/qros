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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('tip_amount', 16, 2)->nullable()->default(0)->after('sub_total');
            $table->text('tip_note')->nullable()->after('tip_amount');
            $table->string('order_status')->default('placed');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->boolean('enable_tip_shop')->default(true);
            $table->boolean('enable_tip_pos')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tip_amount');
            $table->dropColumn('tip_note');
            $table->dropColumn('order_status');
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('enable_tip_shop');
            $table->dropColumn('enable_tip_pos');
        });
    }
};
