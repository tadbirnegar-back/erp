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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('cover_file_id')->nullable();
            $table->string('description')->nullable();
            $table->boolean('isRequired');
            $table->integer('life_cycle');
            $table->unsignedBigInteger('preview_video_id')->nullable();
            $table->float('price');
            $table->unsignedBigInteger('privicy_id');

            $table->foreign('cover_file_id')->references('id')
                ->on('files')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('preview_video_id')->references('id')
                ->on('files')
                ->onDelete('cascade')
                ->onUpdate('cascade');


            $table->foreign('privicy_id')->references('id')
                ->on('privicies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
