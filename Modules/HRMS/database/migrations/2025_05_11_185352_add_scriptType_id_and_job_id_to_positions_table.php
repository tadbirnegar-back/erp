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
        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedBigInteger('script_type_id')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();

            $table->foreign('script_type_id')->references('id')->on('script_types')->onDelete('restrict')->onUpdate('restrict');

            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {

        });
    }
};
