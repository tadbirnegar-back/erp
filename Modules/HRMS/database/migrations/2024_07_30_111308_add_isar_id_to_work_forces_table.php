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
        Schema::table('work_forces', function (Blueprint $table) {

            $table->unsignedBigInteger('isar_id')->nullable();

            $table->foreign('isar_id')->references('id')->on('isars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_forces', function (Blueprint $table) {

        });
    }
};
