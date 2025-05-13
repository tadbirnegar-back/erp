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
        Schema::create('pfm_circulars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('file_id');
            $table->dateTime('created_date');

            $table->foreign('file_id')->references('id')->on('files')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_circulars');
    }
};
