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
        Schema::create('front_review_settings', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->text('reviews')->nullable();
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_designation')->nullable();
            $table->timestamps();
        });

        Schema::create('front_faq_settings', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
            $table->timestamps();
        });
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            $table->string('email')->nullable();
            $table->string('contact_company')->nullable();
            $table->string('image', 200)->nullable()->default(null);
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_reviews');
    }
};
