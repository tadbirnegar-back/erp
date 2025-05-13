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
        Schema::create('pfm_levy_circular', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('levy_id');
            $table->unsignedBigInteger('circular_id');
            $table->dateTime('created_date');

            $table->foreign('levy_id')->references('id')->on('pfm_levies')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('circular_id')->references('id')->on('pfm_circulars')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_levy_circular');
    }
};
