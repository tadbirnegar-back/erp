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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('repository_id');
            $table->unsignedBigInteger('questions_type_id');

            $table->foreign('questions_type_id')->references('id')
                ->on('question_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('repository_id')->references('id')
                ->on('repositories')
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
