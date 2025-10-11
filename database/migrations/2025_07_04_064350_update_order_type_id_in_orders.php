<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\OrderType;

return new class extends Migration
{

    /**
     * Run the migrations.
     */

    public function up(): void
    {
        // Add order_type_id column if it doesn't exist
        if (!Schema::hasColumn('orders', 'order_type_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('order_type_id')->nullable()->after('updated_at');
                $table->foreign('order_type_id')->references('id')->on('order_types')->onDelete('set null');
                $table->string('custom_order_type_name')->nullable()->after('order_type_id');
            });
        }

        // Make order_type column nullable and string if it exists and not already nullable
        if (Schema::hasColumn('orders', 'order_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_type')->nullable()->change();
            });
        }

        // Optimized: Bulk update order_type_id for existing orders using a single query per type
        $orderTypes = OrderType::select('id', 'branch_id', 'order_type_name', 'type')->get();

        foreach ($orderTypes as $orderType) {
            Order::whereNull('order_type_id')
                ->where('branch_id', $orderType->branch_id)
                ->where('order_type', $orderType->type)
                ->update([
                    'order_type_id' => $orderType->id,
                    'custom_order_type_name' => $orderType->order_type_name
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_type_id']);
            $table->dropColumn(['order_type_id', 'custom_order_type_name']);
        });
    }

};
