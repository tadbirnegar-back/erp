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
        Schema::create('acc_articles', function (Blueprint $table) {
            $table->id();

            $table->text('description')->nullable();
            $table->double('debt_amount')->nullable();
            $table->double('credit_amount')->nullable();

            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('document_id');

            $table->foreign('account_id')->references('id')->on('acc_accounts')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('acc_documents')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_articles');
    }
};
