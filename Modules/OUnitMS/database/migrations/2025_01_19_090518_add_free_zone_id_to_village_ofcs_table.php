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
        Schema::table('village_ofcs', function (Blueprint $table) {
            $table->unsignedBigInteger('free_zone_id')->nullable();

            $table->foreign('free_zone_id')->references('id')->on('free_zones_ofcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('village_ofcs', function (Blueprint $table) {

        });
    }
};
