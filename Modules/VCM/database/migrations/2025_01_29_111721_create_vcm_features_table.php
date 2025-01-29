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
        Schema::create('vcm_features', function (Blueprint $table) {
            $table->id();

            $table->longText('description');
            $table->unsignedBigInteger('vcm_version_id');
            $table->unsignedBigInteger('module_id');


            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('vcm_version_id')->references('id')->on('vcm_versions')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vcm_features');
    }
};
