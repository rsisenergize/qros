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
    public function up(): void
    {
        Schema::create('predefined_amounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // Insert default amounts for all existing restaurants
        $restaurants = DB::table('restaurants')->get();
        $defaultAmounts = [50, 100, 500, 1000];

        foreach ($restaurants as $restaurant) {
            foreach ($defaultAmounts as $amount) {
                DB::table('predefined_amounts')->insert([
                    'restaurant_id' => $restaurant->id,
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predefined_amounts');
    }
};
