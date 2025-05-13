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
        Schema::create('smm_circulars', function (Blueprint $table) {
            $table->id();

            $table->string('title')->fulltext();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->unsignedBigInteger('fiscal_year_id');

            $table->unsignedDouble('min_wage', 20, 2)->nullable();
            $table->unsignedDouble('marriage_benefit', 20, 2)->nullable();
            $table->unsignedDouble('rent_benefit', 20, 2)->nullable();
            $table->unsignedDouble('grocery_benefit', 20, 2)->nullable();

            $table->foreign('file_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smm_circulars');
    }
};
