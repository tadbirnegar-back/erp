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
        Schema::table('bdm_estates', function (Blueprint $table) {
            $table->integer('allow_floor')->nullable();
            $table->integer('allow_floor_height')->nullable();
            $table->integer('allow_height')->nullable();
            $table->double('area_after_observe' , 12, 2)->nullable();
            $table->double('area_before_observe' , 12, 2)->nullable();
            $table->double('density_percent')->nullable();
            $table->double('floor_area' , 12, 2)->nullable();
            $table->dateTime('form_date')->nullable();
            $table->longText('form_number')->nullable();
            $table->longText('form_trace_code')->nullable();
            $table->double('occupation_amount' , 12, 2)->nullable();
            $table->double('occupation_percent')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {

        });
    }
};
