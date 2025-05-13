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
        Schema::table('geographic_cordinates', function (Blueprint $table) {
            $table->unsignedBigInteger('dossier_id')->nullable();

            $table->foreign('dossier_id')->references('id')->on('bdm_building_dossiers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geographic_cordinates', function (Blueprint $table) {

        });
    }
};
