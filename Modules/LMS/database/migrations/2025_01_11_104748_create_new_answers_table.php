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
        Schema::table('answers', function (Blueprint $table) {

//            $table->foreign('answer_sheet_id')->references('id')->on('answer_sheets');

            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')
                ->references('id')
                ->on('questions');
//            $table->longText('value')->change()->nullable();
            $table->dropColumn('question_exam_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_answers');
    }
};
