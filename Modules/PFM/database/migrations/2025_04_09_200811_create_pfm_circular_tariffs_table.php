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
        Schema::create('pfm_levy_tariffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('booklet_id');
            $table->unsignedBigInteger('app_id');
            $table->bigInteger('value');
            $table->unsignedBigInteger('creator_id');
            $table->dateTime('created_date');


            $table->foreign('app_id')->references('id')->on('pfm_prop_applications')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('item_id')->references('id')->on('pfm_levy_items')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('booklet_id')->references('id')->on('pfm_circular_booklets')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_circular_tariffs');
    }
};
