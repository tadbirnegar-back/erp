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
        Schema::create('pfm_levy_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prop_application_id');
            $table->unsignedBigInteger('levy_id');
            $table->longText('name');
            $table->dateTime('created_date');

            $table->foreign('prop_application_id')->references('id')->on('pfm_prop_applications')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('levy_id')->references('id')->on('pfm_circular_levies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_circular_items');
    }
};
