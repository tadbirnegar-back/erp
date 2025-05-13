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
        Schema::table('bdm_building_dossiers', function (Blueprint $table) {
            $table->unsignedBigInteger('bill_id')->nullable();

            $table->foreign('bill_id')->references('id')->on('pfm_bills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bdm_building_dossier', function (Blueprint $table) {

        });
    }
};
