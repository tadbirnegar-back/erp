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
        Schema::create('merchandise_products', function (Blueprint $table) {
            $table->id();
            $table->string('package_breadth')->nullable();
            $table->string('package_height')->nullable();
            $table->string('package_weight')->nullable();
            $table->string('package_width')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchandise_products');
    }
};
