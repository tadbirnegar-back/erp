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
        Schema::create('pfm_circular_booklets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('p1')->nullable();
            $table->bigInteger('p2')->nullable();
            $table->bigInteger('p3')->nullable();
            $table->unsignedBigInteger('ounit_id');
            $table->unsignedBigInteger('pfm_circular_id');
            $table->dateTime('created_date');

            $table->foreign('pfm_circular_id')->references('id')->on('pfm_circulars')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('ounit_id')->references('id')->on('organization_units')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_circular_booklet');
    }
};
