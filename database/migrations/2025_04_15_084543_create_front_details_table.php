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
        Schema::create('front_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('header_title', 200)->nullable();
                $table->text('header_description')->nullable();
                $table->string('image', 200)->nullable();
                $table->string('feature_with_image_heading')->nullable();
                $table->string('review_heading')->nullable();
                $table->string('feature_with_icon_heading')->nullable();
                $table->string('comments_heading')->nullable();
                $table->string('price_heading')->nullable();
                $table->string('price_description')->nullable();
                $table->string('faq_heading')->nullable();
                $table->text('faq_description')->nullable();
                $table->text('contact_heading')->nullable();
                $table->text('footer_copyright_text')->nullable();
                 $table->timestamps();
        });

            Schema::create('front_features', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('language_setting_id')->nullable();
                $table->foreign('language_setting_id')
                    ->references('id')
                    ->on('language_settings')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                $table->string('title');
                $table->longText('description')->nullable()->default(null);
                $table->longText('image')->nullable()->default(null);
                $table->longText('icon')->nullable()->default(null);
                $table->enum('type', ['image', 'icon', 'task', 'bills', 'team', 'apps'])->default('image');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_details');
    }
};
