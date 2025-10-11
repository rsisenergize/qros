<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add nullable tax_mode column if it does not exist
        if (!Schema::hasColumn('orders', 'tax_mode')) {
            Schema::table('orders', function (Blueprint $table) {
            $table->string('tax_mode')->nullable()->after('total_tax_amount');
            });
        }

        // Step 2: Update tax_mode efficiently
        DB::table('orders')->orderBy('id')->chunkById(500, function ($orders) {
            $orderIds = $orders->pluck('id')->toArray();

            // Get all taxes in batch
            $taxes = DB::table('order_taxes')
                ->whereIn('order_id', $orderIds)
                ->select('order_id')
                ->distinct()
                ->pluck('order_id')
                ->toArray();

            // Get all items with tax_breakup in batch
            $items = DB::table('order_items')
                ->whereIn('order_id', $orderIds)
                ->select('order_id', 'tax_breakup')
                ->get()
                ->groupBy('order_id');

            foreach ($orders as $order) {
                $mode = null;

                if (in_array($order->id, $taxes)) {
                    $mode = 'order';
                } else {
                    $itemGroup = $items[$order->id] ?? collect();

                    foreach ($itemGroup as $item) {
                        $taxBreakup = is_array($item->tax_breakup)
                            ? $item->tax_breakup
                            : (json_decode($item->tax_breakup, true) ?? []);

                        if (!empty($taxBreakup)) {
                            $mode = 'item';
                            break;
                        }
                    }
                }

                DB::table('orders')->where('id', $order->id)->update([
                    'tax_mode' => $mode,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tax_mode');
        });
    }
};
