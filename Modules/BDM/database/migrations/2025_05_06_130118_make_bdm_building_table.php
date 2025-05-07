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
        Schema::create('bdm_building', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id')->nullable();
            $table->bigInteger('floor_type_id')->nullable();
            $table->bigInteger('floor_number_id')->nullable();
            $table->float('all_corbelling_area' , 12 , 2)->nullable();
            $table->float('floor_height' , 12 , 2)->nullable();
            $table->float('building_area' , 12 , 2)->nullable();
            $table->float('storage_area' , 12 , 2)->nullable();
            $table->float('stairs_area' , 12 , 2)->nullable();
            $table->float('elevator_shaft' , 12 , 2)->nullable();
            $table->float('parking_area' , 12 , 2)->nullable();
            $table->float('corbelling_area' , 12 , 2)->nullable();
            $table->float('duct_area' , 12 , 2)->nullable();
            $table->float('other_parts_area' , 12 , 2)->nullable();
            $table->boolean('is_existed')->default(false);

            $table->foreign('app_id')->references('id')->on('pfm_prop_applications')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdm_building');
    }
};
