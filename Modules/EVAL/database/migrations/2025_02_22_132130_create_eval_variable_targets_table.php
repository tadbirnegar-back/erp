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
        Schema::create('eval_variable_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eval_circular_variables_id');
            $table->foreign('eval_circular_variables_id')->references('id')
                ->on('eval_circular_variables')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('ouc_property_value_id');
            $table->foreign('ouc_property_value_id')->references('id')
                ->on('ouc_property_values')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_variable_targets');
    }
};
