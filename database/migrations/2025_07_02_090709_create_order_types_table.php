<?php

use App\Models\Branch;
use App\Models\OrderType;
use App\Models\Restaurant;
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
        Schema::create('order_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->string('order_type_name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('type')->nullable();
            $table->timestamps();
        });

        $branches = Branch::all();

        foreach ($branches as $branch) {
            // Insert default order types for each branch
            DB::table('order_types')->insert([
            [
                'branch_id' => $branch->id,
                'order_type_name' => 'Dine In',
                'slug' => 'dine_in',
                'is_active' => true,
                'is_default' => true,
                'type' => 'dine_in',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => $branch->id,
                'order_type_name' => 'Delivery',
                'slug' => 'delivery',
                'is_active' => true,
                'is_default' => true,
                'type' => 'delivery',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_id' => $branch->id,
                'order_type_name' => 'Pickup',
                'slug' => 'pickup',
                'is_active' => true,
                'is_default' => true,
                'type' => 'pickup',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_types');
    }

};
