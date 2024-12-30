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
        Schema::create('bgt_circular_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('circular_id');
            $table->unsignedBigInteger('subject_id');

            $table->foreign('circular_id')->references('id')->on('bgt_circulars')->onDelete('cascade');

            $table->foreign('subject_id')->references('id')->on('bgt_circular_subjects')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgt_circular_items');
    }
};
