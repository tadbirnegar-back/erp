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
        Schema::disableForeignKeyConstraints();
        Schema::create('village_ofcs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('town_ofc_id');


            $table->foreign('town_ofc_id')->references('id')->on('town_ofcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_ofcs');
    }
};
