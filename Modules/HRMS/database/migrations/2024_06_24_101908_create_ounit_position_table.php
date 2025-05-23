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
        Schema::create('ounit_position', function (Blueprint $table) {
            $table->unsignedBigInteger('ounit_id');
            $table->unsignedBigInteger('position_id');

            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ounit_position');
    }
};
