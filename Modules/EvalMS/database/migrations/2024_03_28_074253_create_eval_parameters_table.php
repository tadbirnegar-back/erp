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
        Schema::create('eval_parameters', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('weight');
            $table->text('circle_of_inclusion')->default('کلیه دهیاری ها')->nullable();
            $table->text('related_legal_citation')->nullable();

            $table->unsignedBigInteger('eval_indicator_id');
            $table->unsignedBigInteger('eval_parameter_type_id');

            $table->foreign('eval_indicator_id')->references('id')->on('evaluator_indicators')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('eval_parameter_type_id')->references('id')->on('eval_parameter_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_parameters');
    }
};
