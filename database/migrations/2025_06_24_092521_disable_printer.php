<?php

use App\Models\Printer;
use App\Models\Restaurant;
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
        // Use mass update instead of individual saves for better performance
        Printer::where('restaurant_id', '>', 0)
            ->update(['printing_choice' => 'browserPopupPrint']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to direct print if needed
        Printer::where('restaurant_id', '>', 0)
            ->update(['printing_choice' => 'directPrint']);
    }
};
