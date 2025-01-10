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
        Schema::create('accDocument_status', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('creator_id');
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('document_id')->references('id')->on('acc_documents')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accDocument_status');
    }
};
