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
        // Check if unique constraint exists before dropping
        $indexExists = $this->indexExists('customers', 'customers_phone_unique');

        if ($indexExists) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropUnique(['phone']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if unique constraint doesn't exist before adding
        $indexExists = $this->indexExists('customers', 'customers_phone_unique');

        if (!$indexExists) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unique('phone');
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->selectOne(
            "SELECT COUNT(*) as count
             FROM information_schema.statistics
             WHERE table_schema = ?
             AND table_name = ?
             AND index_name = ?",
            [$database, $table, $index]
        );

        return $result->count > 0;
    }
};
