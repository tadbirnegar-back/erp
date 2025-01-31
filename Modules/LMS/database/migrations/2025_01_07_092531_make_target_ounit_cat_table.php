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
        Schema::create('target_ounit_cat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_target_id');
            $table->unsignedBigInteger('ounit_cat_id');

            $table->foreign('course_target_id')->references('id')->on('course_targets')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_ounit_cat');
    }
};
