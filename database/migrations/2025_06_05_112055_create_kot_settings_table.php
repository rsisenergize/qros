<?php

use App\Models\KotSetting;
use App\Models\Branch;
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
        Schema::create('kot_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->enum('default_status', ['pending', 'cooking'])->default('pending');
            $table->boolean('enable_item_level_status')->default(true);
            $table->timestamps();
        });

        Schema::table('kot_items', function (Blueprint $table) {
            $table->enum('status', ['pending', 'cooking', 'ready'])->nullable()->after('quantity')->change();
        });

        $checkBranch = Branch::select('id')->get();

        if ($checkBranch->count() > 0) {
            foreach ($checkBranch as $branch) {
                KotSetting::create([
                    'branch_id' => $branch->id,
                    'default_status' => 'pending',
                    'enable_item_level_status' => true,
                ]);
            }
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kot_items', function (Blueprint $table) {
            $table->enum('status', ['cooking', 'ready'])->nullable()->change();
        });
    
        Schema::dropIfExists('kot_settings');
    
    }
};
