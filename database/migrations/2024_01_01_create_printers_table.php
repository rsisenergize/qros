<?php

use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up()
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['network', 'windows', 'linux']);
            $table->enum('profile', ['default', 'simple', 'SP2000', 'TEP-200M', 'P822D']);
            $table->integer('char_per_line');
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('path')->nullable();
            $table->string('printer_name')->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('printers');
    }

};
