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
        Schema::create('pfm_levies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('bgt_subject_id');
            $table->longText('category')->nullable();
            $table->string('description')->nullable();
            $table->boolean('has_app');
            $table->unsignedBigInteger('status_id');


            $table->foreign('bgt_subject_id')->references('id')->on('bgt_circular_subjects')
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
        Schema::dropIfExists('pfm_circulars_fees');
    }
};
