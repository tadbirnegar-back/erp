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
        Schema::create('ounitAcc_Imports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ounit_id');
            $table->unsignedBigInteger('creator_id');
            $table->dateTime('create_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ounitAcc_Imports');
    }
};
