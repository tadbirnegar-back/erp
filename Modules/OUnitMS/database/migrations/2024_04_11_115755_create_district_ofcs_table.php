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
        Schema::create('district_ofcs', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();

            $table->id();

            $table->unsignedBigInteger('city_ofc_id');


            $table->foreign('city_ofc_id')->references('id')->on('city_ofcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_ofcs');
    }
};
