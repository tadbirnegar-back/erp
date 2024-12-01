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
        schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_type_id');
            $table->unsignedBigInteger('file_id')->nullable();
            $table->unsignedBigInteger('lesson_id');
            $table->string('name');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('teacher_id');


            $table->foreign('content_type_id')->references('id')
                ->on('content_type')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('file_id')->references('id')
                ->on('files')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('lesson_id')->references('id')
                ->on('lessons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('status_id')->references('id')
                ->on('statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('teacher_id')->references('id')
                ->on('teachers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
