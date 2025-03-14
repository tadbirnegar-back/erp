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
        Schema::table('acc_articles', function (Blueprint $table) {

            $table->unsignedBigInteger('transaction_id')->nullable()->index();

            $table->foreign('transaction_id')->references('id')->on('bnk_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acc_articles', function (Blueprint $table) {

        });
    }
};
