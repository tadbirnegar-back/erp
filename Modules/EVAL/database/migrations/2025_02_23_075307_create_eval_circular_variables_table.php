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
        Schema::create('eval_circular_variables', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('title');
            $table->double('weight');
            $table->unsignedBigInteger('eval_circular_indicator_id');
            $table->foreign('eval_circular_indicator_id')->references('id')
                ->on('eval_circular_indicators')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_circular_variables');
    }
};
