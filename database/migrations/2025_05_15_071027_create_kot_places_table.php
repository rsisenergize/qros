<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Branch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up(): void
    {
        // Step 1: Add kot_place_id column WITHOUT foreign key
        if (!Schema::hasColumn('menu_items', 'kot_place_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->unsignedBigInteger('kot_place_id')->nullable()->after('branch_id');
            });
        }

        // Step 2: Create kot_places table
        if (!Schema::hasTable('kot_places')) {
            Schema::create('kot_places', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('printer_id')->nullable();
                $table->foreign('printer_id')->references('id')->on('printers')->onDelete('cascade');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                $table->string('name');
                $table->string('type')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // Step 3: Create order_places table
        if (!Schema::hasTable('order_places')) {
            Schema::create('order_places', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('printer_id')->nullable();
                $table->foreign('printer_id')->references('id')->on('printers')->onDelete('cascade');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                $table->string('name');
                $table->string('type')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // Step 4: Now add the FOREIGN KEY after kot_places is created
        if (Schema::hasColumn('menu_items', 'kot_place_id') && !Schema::hasColumn('menu_items', 'kot_place_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->foreign('kot_place_id')->references('id')->on('kot_places')->nullOnDelete();
            });
        }

        // Step 5: Create data using models
        $branches = Branch::all();

        foreach ($branches as $branch) {
            // Create default KOT place
            $kotPlace = $branch->kotPlaces()->create([
                'name' => 'Default Kitchen',
                'branch_id' => $branch->id,
                'printer_id' => null,
                'type' => 'food',
                'is_active' => true,
                'is_default' => true,
            ]);

            // Assign kot_place_id to menu_items
            DB::table('menu_items')
                ->where('branch_id', $branch->id)
                ->update(['kot_place_id' => $kotPlace->id]);

            // Create default Order place
            $orderPlace = $branch->orderPlaces()->create([
                'name' => 'Default POS Terminal',
                'branch_id' => $branch->id,
                'printer_id' => null,
                'type' => 'vegetarian',
                'is_active' => true,
                'is_default' => true,
            ]);

            try {
                // Create printer
                $printer = $branch->printerSettings()->create([
                    'name' => 'Default Thermal Printer',
                    'restaurant_id' => $branch->restaurant_id,
                    'branch_id' => $branch->id,
                    'is_active' => true,
                    'is_default' => true,
                    'printing_choice' => 'browserPopupPrint',
                    'kots' => [$kotPlace->id],
                    'orders' => [$orderPlace->id],
                ]);
            } catch (\Exception $e) {
                dd($e);
            }

            // Update kot_place and order_place with printer_id
            $kotPlace->update(['printer_id' => $printer->id]);
            $orderPlace->update(['printer_id' => $printer->id]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('menu_items', 'kot_place_id')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropForeign(['kot_place_id']);
                $table->dropColumn('kot_place_id');
            });
        }

        Schema::dropIfExists('kot_places');
        Schema::dropIfExists('order_places');
    }
};
