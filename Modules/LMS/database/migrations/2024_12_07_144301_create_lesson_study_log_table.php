<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_study_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('study_count');
            $table->dateTime('last_study_date');
            $table->boolean('is_completed')->default(false);

            $table->foreign('lesson_id')->references('id')->on('lessons')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('student_id')->references('id')->on('students')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
