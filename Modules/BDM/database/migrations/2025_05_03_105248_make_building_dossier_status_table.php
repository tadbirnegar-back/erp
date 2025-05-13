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
        Schema::create('bdm_building_dossier_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dossier_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('creator_id');
            $table->dateTime('created_date');
            $table->foreign('dossier_id')->references('id')->on('bdm_building_dossiers')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_dossier_status');
    }
};
