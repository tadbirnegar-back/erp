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
        Schema::create('bdm_dossier_lawyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dossier_id');
            $table->unsignedBigInteger('lawyer_id');

            $table->foreign('dossier_id')->references('id')->on('bdm_building_dossiers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('lawyer_id')->references('id')->on('bdm_lawyers')->onDelete('cascade')->onUpdate('cascade');
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
