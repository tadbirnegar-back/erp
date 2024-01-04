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

            $table->string('name');
            $table->string('registration_number');
            $table->dateTime('foundation_date');

            $table->unsignedBigInteger('legal_type_id');
            $table->unsignedBigInteger('address_id');

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
