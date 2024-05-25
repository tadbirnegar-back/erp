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
        Schema::create('educational_records', function (Blueprint $table) {
            $table->id();

            $table->string('university_name');
            $table->string('field_of_study');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('average')->nullable();
            $table->unsignedBigInteger('work_force_id');
            $table->unsignedBigInteger('level_of_educational_id')->nullable();


            $table->foreign('level_of_educational_id')->references('id')->on('levels_of_education')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('work_force_id')->references('id')->on('work_forces')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_records');
    }
};
