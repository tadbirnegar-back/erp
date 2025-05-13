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
        Schema::table('bdm_pools', function (Blueprint $table) {
            $table->unsignedBigInteger('app_id')->nullable();

            $table->foreign('app_id')->references('id')->on('pfm_prop_applications')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bdm_pool', function (Blueprint $table) {

        });
    }
};
