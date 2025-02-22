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
        Schema::create('eval_evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('eval_circular_variables_id');
            $table->foreign('eval_circular_variables_id')->references('id')
                ->on('eval_circular_variables')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('eval_evaluation_id');
            $table->foreign('eval_evaluation_id')->references('id')
                ->on('eval_evaluations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_evaluation_answers');
    }
};
