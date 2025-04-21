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
        Schema::create('pfm_circular_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pfm_circular_id');
            $table->longText('description');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('created_date');
            $table->unsignedBigInteger('creator_id');

            $table->foreign('pfm_circular_id')->references('id')->on('pfm_circulars')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('status_id')->references('id')->on('statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('creator_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_circular_statuses');
    }
};
