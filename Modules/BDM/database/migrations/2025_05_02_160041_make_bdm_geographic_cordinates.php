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
        Schema::create('geographic_cordinates', function (Blueprint $table) {
            $table->id();
            $table->longText('west')->nullable();
            $table->longText('east')->nullable();
            $table->longText('north')->nullable();
            $table->longText('south')->nullable();
            $table->integer('type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('');
    }
};
