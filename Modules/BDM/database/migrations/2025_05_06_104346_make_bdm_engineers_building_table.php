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
        Schema::create('bdm_engineers_building', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engineer_id');
            $table->unsignedBigInteger('dossier_id');

            $table->foreign('engineer_id')->references('id')->on('bdm_engineers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dossier_id')->references('id')->on('bdm_building_dossiers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdm_engineers_building');
    }
};
