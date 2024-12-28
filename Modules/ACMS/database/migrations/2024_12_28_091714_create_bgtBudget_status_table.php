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
        Schema::create('bgtBudget_status', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('creator_id')->nullable();

            $table->dateTime('create_date')->useCurrent();
   

            $table->foreign('budget_id')->references('id')->on('bgt_budgets')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgtBudget_status');
    }
};
