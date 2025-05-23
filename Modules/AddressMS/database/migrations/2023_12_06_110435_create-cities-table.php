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
        Schema::create('cities', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();

            $table->id();
            $table->string('name')->fulltext();
            $table->string('amar_code')->index();

            $table->unsignedBigInteger('state_id');

            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade')->onUpdate('cascade');
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
