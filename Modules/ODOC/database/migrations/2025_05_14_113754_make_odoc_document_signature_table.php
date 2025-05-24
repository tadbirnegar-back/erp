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
        Schema::create('odoc_document_signature', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('odoc_document_id');
            $table->unsignedBigInteger('signature_id');
            $table->dateTime('signed_date')->nullable();

            $table->foreign('odoc_document_id')->references('id')->on('odoc_documents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('signature_id')->references('id')->on('signatures')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odoc_document_signature');
    }
};
