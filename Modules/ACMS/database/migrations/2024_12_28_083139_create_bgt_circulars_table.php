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
        Schema::create('bgt_circulars', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedBigInteger('file_id')->nullable();
            $table->unsignedBigInteger('fiscal_year_id');

            $table->foreign('file_id')->references('id')->on('files')->onDelete('set null');
            
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgt_circulars');
    }
};
