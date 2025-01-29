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
        Schema::create('vcm_versions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('create_date');
            $table->integer('high_version');
            $table->integer('mid_version');
            $table->integer('low_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vcm_versions');
    }
};
