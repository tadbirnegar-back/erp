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
        Schema::create('bnk_transactions', function (Blueprint $table) {
            $table->id();

            $table->double('deposit')->nullable();
            $table->double('withdrawal')->nullable();
            $table->double('transfer')->nullable();
            $table->nullableMorphs('transactionable');
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('creator_id')->nullable()->index();
            $table->unsignedBigInteger('cheque_id')->nullable()->index();
            $table->unsignedBigInteger('card_id')->nullable()->index();

            $table->dateTime('create_date')->useCurrent();

            $table->foreign('account_id')->references('id')->on('acc_accounts')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('cheque_id')->references('id')->on('bnk_cheques')->onDelete('set null');
            $table->foreign('card_id')->references('id')->on('bnk_accounts_cards')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnk_transactions');
    }
};
