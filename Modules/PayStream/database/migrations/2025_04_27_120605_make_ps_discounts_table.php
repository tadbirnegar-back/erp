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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('max_usage')->nullable();
            $table->longText('order_type');
            $table->dateTime('expired_date')->nullable();
            $table->dateTime('created_date');
            $table->longText('title');
            $table->bigInteger('value');
            $table->unsignedBigInteger('value_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ps_discounts');
    }
};
