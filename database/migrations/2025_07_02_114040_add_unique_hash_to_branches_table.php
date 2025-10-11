<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('branches', 'unique_hash')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('unique_hash', 64)->unique()->nullable()->after('id');
            });

            if (!Schema::hasIndex('orders', 'idx_branch_date')) {
                // Add indexes to frequently queried columns
                Schema::table('orders', function (Blueprint $table) {
                    $table->index(['branch_id', 'date_time'], 'idx_branch_date');
                });
                Schema::table('menu_items', function (Blueprint $table) {
                    $table->index(['branch_id', 'is_available'], 'idx_branch_available');
                });
                Schema::table('users', function (Blueprint $table) {
                    $table->index(['branch_id', 'email'], 'idx_branch_email');
                });
            }
        }



        // Generate unique hashes for existing branches
        $this->generateHashesForExistingBranches();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('unique_hash');
        });
    }

    /**
     * Generate unique hashes for existing branches
     */
    public function generateHashesForExistingBranches(): void
    {
        $branches = Branch::whereNull('unique_hash')->get();

        foreach ($branches as $branch) {
            $branch->generateUniqueHash();
            $branch->save();
        }
    }
};
