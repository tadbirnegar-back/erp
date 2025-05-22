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
        Schema::create('bdm_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dossier_id');
            $table->unsignedBigInteger('report_type_id');
            $table->longText('description');
            $table->dateTime('created_date');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('file_id');

            $table->foreign('dossier_id')->references('id')
                ->on('bdm_building_dossiers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('report_type_id')->references('id')
                ->on('bdm_report_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('creator_id')->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('file_id')->references('id')
                ->on('files')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdm_reports');
    }
};
