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
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->string('position');
            $table->string('salary')->nullable();
            $table->unsignedBigInteger('work_force_id');


            $table->foreign('work_force_id')->references('id')->on('work_forces')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
