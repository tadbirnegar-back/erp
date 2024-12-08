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
        Schema::create('card_to_card_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_to_card_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('card_to_card_id')->references('id')
                ->on('card_to_cards')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_user');
    }
};
