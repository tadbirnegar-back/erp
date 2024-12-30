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
        Schema::create('ounit_fiscalYear', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ounit_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('closer_id')->nullable();

            $table->dateTime('create_date')->useCurrent();
            $table->dateTime('close_date')->nullable();

            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade');

            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            $table->foreign('closer_id')->references('id')->on('users')->onDelete('set null');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ounit_fiscalYear');
    }
};
