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
        Schema::create('process_status', function (Blueprint $table) {
            $table->id();
            $table -> unsignedBigInteger('order_id');
            $table -> unsignedBigInteger('status_id');
            $table -> unsignedBigInteger('creator_id');
            $table->dateTime('created_date');

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_status');
    }
};
