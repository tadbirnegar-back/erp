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
        Schema::create('bgt_budgets', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('isSupplementary');
            $table->unsignedBigInteger('ounitFiscalYear_id');
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('parent_id')->references('id')->on($table->getTable())->onDelete('set null');
            $table->foreign('ounitFiscalYear_id')->references('id')->on('ounit_fiscalYear')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgt_budgets');
    }
};
