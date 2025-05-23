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
        Schema::create('evaluator_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('coefficient');

            $table->unsignedBigInteger('eval_part_id');

            $table->foreign('eval_part_id')->references('id')->on('eval_parts')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluator_indicators');
    }
};
