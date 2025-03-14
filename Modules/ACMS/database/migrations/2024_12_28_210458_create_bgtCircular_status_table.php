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
        Schema::disableForeignKeyConstraints();
        Schema::create('bgtCircular_status', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('circular_id');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('circular_id')->references('id')->on('bgt_circulars')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgtCircular_status');
    }
};
