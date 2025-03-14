<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bnkCheque_status', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cheque_id');
            $table->unsignedBigInteger('status_id');
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('cheque_id')->references('id')->on('bnk_cheques')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnkCheque_status');
    }
};
