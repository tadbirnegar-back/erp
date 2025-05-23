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
        Schema::create('city_ofcs', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();

            $table->id();

            $table->unsignedBigInteger('state_ofc_id');


            $table->foreign('state_ofc_id')->references('id')->on('state_ofcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_ofcs');
    }
};
