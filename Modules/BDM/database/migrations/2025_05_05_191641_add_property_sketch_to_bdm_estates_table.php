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
            $table->unsignedBigInteger('propery_sketch_file_id')->nullable();

            $table->foreign('propery_sketch_file_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bdm_estates', function (Blueprint $table) {

        });
    }
};
