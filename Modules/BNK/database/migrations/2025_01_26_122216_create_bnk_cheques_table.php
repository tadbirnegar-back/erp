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
        Schema::create('bnk_cheques', function (Blueprint $table) {
            $table->id();

            $table->string('payee_name')->nullable()->index();
            $table->string('segment_number')->index();
            $table->unsignedBigInteger('amount')->nullable()->default(0);
            $table->unsignedBigInteger('cheque_book_id');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('signed_date')->nullable();

            $table->foreign('cheque_book_id')->references('id')->on('bnk_cheque_books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnk_cheques');
    }
};
