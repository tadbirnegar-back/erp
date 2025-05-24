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
        Schema::create('odoc_documents', function (Blueprint $table) {
            $table->id();
            $table->string('component_to_render')->nullable();
            $table->longText('data')->nullable();
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->nullable();
            $table->longText('serial_number')->nullable();
            $table->longText('title')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->dateTime('creator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odoc_documents');
    }
};
