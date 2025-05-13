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
            $table->bigInteger('building_status_id')->nullable();
            $table->bigInteger('field_status_id')->nullable();
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
