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
        Schema::create('answer_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->dateTime('finish_date_time');
            $table->dateTime('start_date_time');
            $table->float('score');


            $table->foreign('exam_id')->references('id')
                ->on('exams')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('student_id')->references('id')
                ->on('students')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
