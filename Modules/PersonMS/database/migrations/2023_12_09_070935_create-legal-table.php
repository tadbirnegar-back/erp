<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('legals', function (Blueprint $table) {
            $table->id();

            $table->string('name')->fulltext();
            $table->string('registration_number')->nullable();
            $table->dateTime('foundation_date')->nullable();

            $table->unsignedBigInteger('legal_type_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();

            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('legal_type_id')->references('id')->on('legal_type')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
