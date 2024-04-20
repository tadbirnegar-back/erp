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
        Schema::create('town_ofcs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('district_ofc_id');


            $table->foreign('district_ofc_id')->references('id')->on('district_ofcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('town_ofcs');
    }
};
