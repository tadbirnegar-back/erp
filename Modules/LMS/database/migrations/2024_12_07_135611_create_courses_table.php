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
            $table->integer('price');
            $table->unsignedBigInteger('preview_video_id');
            $table->boolean('is_required');
            $table->dateTime('expiration_date');
            $table->string('description');
            $table->unsignedBigInteger('creator_id');
            $table->dateTime('created_date');
            $table->unsignedBigInteger('cover_id');
            $table->dateTime('access_date');
            $table->unsignedBigInteger('privacy_id');

            $table->foreign('preview_video_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cover_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('privacy_id')->references('id')->on('privicies')->onUpdate('cascade')->onDelete('cascade');
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
