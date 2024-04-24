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
        Schema::table('evaluators', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_unit_id')->nullable();

            $table->foreign('organization_unit_id')->references('id')->on('organization_units')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluators', function (Blueprint $table) {

        });
    }
};
