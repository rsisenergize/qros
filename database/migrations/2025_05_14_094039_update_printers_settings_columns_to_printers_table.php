<?php

use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            // Add new columns
            $table->unsignedBigInteger('restaurant_id')->nullable()->after('id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('restaurant_id');
            $table->string('printing_choice')->nullable()->after('name');
            $table->text('kots')->nullable()->after('printing_choice');
            $table->text('orders')->nullable()->after('kots');
            $table->string('print_format')->nullable()->after('orders');
            $table->integer('invoice_qr_code')->nullable()->after('print_format');
            $table->enum('open_cash_drawer', ['yes', 'no'])->nullable()->after('invoice_qr_code');
            $table->string('ipv4_address')->nullable()->after('open_cash_drawer');
            $table->string('thermal_or_nonthermal')->nullable()->after('ipv4_address');
            $table->string('share_name')->nullable()->after('thermal_or_nonthermal');
            $table->boolean('is_active')->default(true)->after('profile');
            $table->boolean('is_default')->default(false)->after('is_active');

            // Modify enum columns
            $table->enum('profile', ['default', 'simple', 'SP2000', 'TEP-200M', 'P822D'])->nullable()->change();
            $table->enum('type', ['network', 'windows', 'linux', 'default'])->nullable()->change();
            $table->integer('char_per_line')->nullable()->change();

            // Foreign keys
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Revert any inserted printers marked as default (optional)
        DB::table('printers')->where('is_default', true)->delete();

        Schema::table('printers', function (Blueprint $table) {
            // Restore enum types if needed
            $table->enum('profile', ['default', 'simple', 'SP2000', 'TEP-200M', 'P822D'])->nullable(false)->change();
            $table->enum('type', ['network', 'windows', 'linux'])->nullable(false)->change();
            $table->integer('char_per_line')->nullable(false)->change();

            // Drop foreign keys first
            $table->dropForeign(['restaurant_id']);
            $table->dropForeign(['branch_id']);

            // Then drop columns in reverse order of addition
            $table->dropColumn([
                'share_name',
                'thermal_or_nonthermal',
                'ipv4_address',
                'open_cash_drawer',
                'invoice_qr_code',
                'print_format',
                'orders',
                'kots',
                'printing_choice',
                'is_default',
                'is_active',
                'branch_id',
                'restaurant_id',
            ]);
        });
    }

};
