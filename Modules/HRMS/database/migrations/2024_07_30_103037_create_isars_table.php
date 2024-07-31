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
        Schema::create('isars', function (Blueprint $table) {
            $table->id();
            $table->string('length')->nullable();
            $table->unsignedInteger('percentage')->nullable();
            $table->unsignedBigInteger('isar_status_id');

            $table->unsignedBigInteger('relative_type_id');

            $table->foreign('isar_status_id')->references('id')->on('isar_statuses')->onDelete('cascade');

            $table->foreign('relative_type_id')->references('id')->on('relative_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isars');
    }
};
