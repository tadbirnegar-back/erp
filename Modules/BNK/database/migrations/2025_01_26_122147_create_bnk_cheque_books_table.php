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
        Schema::disableForeignKeyConstraints();
        Schema::create('bnk_cheque_books', function (Blueprint $table) {
            $table->id();

            $table->string('cheque_series')->nullable()->index();
            $table->unsignedInteger('cheque_count')->nullable()->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('creator_id')->nullable()->index();
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('account_id')->references('id')->on('bnk_bank_accounts')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnk_cheque_books');
    }
};

