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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('signature_file_id')->nullable();
            $table->unsignedBigInteger('signature_license_id')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->dateTime('created_date');

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('signature_file_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('signature_license_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
