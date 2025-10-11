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
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('printer_id')->nullable();
            $table->foreign('printer_id')->references('id')->on('printers')->onDelete('cascade');

            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status')->default('pending'); // pending | printing | done | failed
            $table->string('response_printer')->nullable();        // human-readable printer name
            $table->longText('payload');                      // anything you want the printer to output
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
