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
        Schema::create('odoc_document_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('odoc_document_id');
            $table->unsignedBigInteger('status_id');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->dateTime('created_date')->nullable();

            $table->foreign('odoc_document_id')->references('id')->on('odoc_documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odoc_document_status');
    }
};
