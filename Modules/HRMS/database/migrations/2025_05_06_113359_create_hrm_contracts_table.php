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
        Schema::create('hrm_contracts', function (Blueprint $table) {
            $table->id();
            $table->boolean('isMarried');
            $table->boolean('hasIsar');
            $table->unsignedInteger('number_of_kids');
            $table->unsignedBigInteger('script_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('script_id')->references('id')->on('recruitment_scripts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_contracts');
    }
};
