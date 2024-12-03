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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('difficulty_id')->nullable();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('question_type_id');
            $table->unsignedBigInteger('repository_id');
            $table->unsignedBigInteger('status_id');


            $table->foreign('creator_id')->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('difficulty_id')->references('id')
                ->on('difficulties')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('question_type_id')->references('id')
                ->on('question_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('repository_id')->references('id')
                ->on('repositories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('status_id')->references('id')
                ->on('statuses')
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
