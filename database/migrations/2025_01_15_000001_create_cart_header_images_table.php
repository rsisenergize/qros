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
        Schema::create('cart_header_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_header_setting_id');
            $table->foreign('cart_header_setting_id')->references('id')->on('cart_header_settings')->onDelete('cascade');
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_header_images');
    }
}; 