<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_ounit_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_target_id');
            $table->unsignedBigInteger('ouc_property_value');

            $table->foreign('course_target_id')->references('id')->on('course_targets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('ouc_property_value')->references('id')->on('ouc_property_values')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
