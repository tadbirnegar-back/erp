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
        Schema::create('pfm_booklet_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booklet_id');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('created_date');
            $table->unsignedBigInteger('creator_id');




            $table->foreign('creator_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('booklet_id')->references('id')->on('pfm_circular_booklets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('status_id')->references('id')->on('statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booklet_status');
    }
};
