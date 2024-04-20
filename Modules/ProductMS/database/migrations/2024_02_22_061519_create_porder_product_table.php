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
        Schema::create('porder_product', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('porder_id')->index();
            $table->unsignedBigInteger('product_id')->index();

            $table->foreign('porder_id')->references('id')->on('porders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porder_product');
    }
};
