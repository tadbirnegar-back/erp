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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();

            $table->string('label');
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedBigInteger('part_id')->index()->nullable();
            $table->unsignedBigInteger('type_id')->index()->nullable();


            $table->foreign('part_id')->references('id')->on('parts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('type_id')->references('id')->on('field_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
