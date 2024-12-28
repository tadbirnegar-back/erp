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
        Schema::create('bgt_budgets', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('isSupplementary');
            $table->unsignedBigInteger('ounit_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('parent_id')->references('id')->on($table->getTable())->onDelete('set null');
            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade');
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');

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
