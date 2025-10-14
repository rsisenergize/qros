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
        if (!Schema::hasTable('kiosks')) {
            Schema::create('kiosks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $table->string('code', 64)->unique();        // e.g. KSK-01
                $table->string('name', 100)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('orders', 'kiosk_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('kiosk_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('placed_via', ['pos', 'shop', 'kiosk'])->nullable()->change();
            });
        }

        if (!Schema::hasColumn('cart_sessions', 'kiosk_id')) {
            Schema::table('cart_sessions', function (Blueprint $table) {
                $table->foreignId('kiosk_id')->nullable()->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('cart_items', 'kiosk_id')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreignId('kiosk_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosks');
    }
};
