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
        Schema::create('eval_parameter_answers', function (Blueprint $table) {
            $table->id();
            $table->longText('value');

            $table->unsignedBigInteger('eval_parameter_id');
            $table->unsignedBigInteger('evaluator_id');

            $table->foreign('eval_parameter_id')->references('id')->on('eval_parameters')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('evaluator_id')->references('id')->on('evaluators')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_parameter_answers');
    }
};
