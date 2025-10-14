<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {

        Schema::table('inventory_items', function (Blueprint $table) {
            // Check if foreign key exists before trying to drop it
            $foreignKeys = [];
            $foreignKeyName = null;

            // Get foreign keys using DB facade instead of Doctrine
            $constraints = DB::select(
                "SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND TABLE_NAME = 'inventory_items'
                AND TABLE_SCHEMA = DATABASE()"
            );

            foreach ($constraints as $constraint) {
                $keyName = $constraint->CONSTRAINT_NAME;
                $columns = DB::select(
                    "SELECT COLUMN_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE CONSTRAINT_NAME = ?
                    AND TABLE_NAME = 'inventory_items'
                    AND TABLE_SCHEMA = DATABASE()",
                    [$keyName]
                );

                $columnNames = array_map(function($col) {
                    return $col->COLUMN_NAME;
                }, $columns);

                if (in_array('preferred_supplier_id', $columnNames)) {
                    $foreignKeyName = $keyName;
                    break;
                }
            }

            if ($foreignKeyName) {
                $table->dropForeign($foreignKeyName);
            }

            // Add the new foreign key constraint
            $table->foreign('preferred_supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
