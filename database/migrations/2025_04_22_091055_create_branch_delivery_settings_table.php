<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch_delivery_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('max_radius', 8, 2)->default(5.00);
            $table->enum('unit', ['km', 'miles'])->default('km');
            $table->string('fee_type')->default('fixed');
            $table->decimal('fixed_fee', 8, 2)->nullable();
            $table->decimal('per_distance_rate', 8, 2)->nullable();
            $table->decimal('free_delivery_over_amount', 8, 2)->nullable();
            $table->float('free_delivery_within_radius')->nullable();
            $table->time('delivery_schedule_start')->nullable();
            $table->time('delivery_schedule_end')->nullable();
            $table->integer('prep_time_minutes')->default(20);
            $table->integer('additional_eta_buffer_time')->nullable();  
            $table->integer('avg_delivery_speed_kmh')->default(30);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_delivery_settings');
    }
};
