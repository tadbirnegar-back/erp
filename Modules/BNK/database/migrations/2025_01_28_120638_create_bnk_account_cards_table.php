<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\BNK\app\Models\BankAccount;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bnk_account_cards', function (Blueprint $table) {
            $table->id();

            $table->string('card_number', 16)->unique();
            $table->unsignedBigInteger('account_id');
            $table->dateTime('expire_date')->nullable();

            $table->foreign('account_id')->references('id')->on(BankAccount::getTableName())->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnk_account_cards');
    }
};
