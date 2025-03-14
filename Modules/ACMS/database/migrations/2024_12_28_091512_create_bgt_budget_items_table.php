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
        Schema::create('bgt_budget_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedDecimal('proposed_amount', 65, 2);
            $table->unsignedDecimal('finalized_amount', 65, 2);
            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('circular_item_id');

            $table->foreign('budget_id')->references('id')->on('bgt_budgets')->onDelete('cascade');
            $table->foreign('circular_item_id')->references('id')->on('bgt_circular_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgt_budget_items');
    }
};
