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
        Schema::create('bdm_partitioning', function (Blueprint $table) {
            $table->id();
            $table->float('height' , 12 , 2)->nullable();
            $table->bigInteger('partitioning_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdm_partitioning');
    }
};
