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
        Schema::create('relatives', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('birthdate')->nullable();
            $table->string('mobile');
            $table->unsignedBigInteger('level_of_educational_id')->nullable();
            $table->unsignedBigInteger('relative_type_id')->nullable();
            $table->unsignedBigInteger('work_force_id');

            $table->foreign('level_of_educational_id')->references('id')->on('levels_of_educational')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('relative_type_id')->references('id')->on('relative_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('work_force_id')->references('id')->on('work_forces')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatives');
    }
};
