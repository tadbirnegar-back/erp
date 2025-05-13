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
        Schema::create('pfm_levy_bill', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('levy_id');
            $table->unsignedBigInteger('bill_id');

            $table->longText('key');
            $table->longText('value');

            $table->foreign('levy_id')->references('id')->on('pfm_levies')->onDelete('cascade');
            $table->foreign('bill_id')->references('id')->on('pfm_bills')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pfm_levy_bill');
    }
};
