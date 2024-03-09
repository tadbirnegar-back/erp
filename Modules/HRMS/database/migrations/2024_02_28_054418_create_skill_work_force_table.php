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
        Schema::create('skill_work_force', function (Blueprint $table) {
            $table->id();
            $table->integer('percentage');

            $table->unsignedBigInteger('skill_id');
            $table->unsignedBigInteger('work_force_id');


            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('work_force_id')->references('id')->on('work_forces')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_work_force');
    }
};
