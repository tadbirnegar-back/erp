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
        Schema::create('eval_circular_indicators', function (Blueprint $table) {
            $table->id();
            $table->double('coefficient');
            $table->string('title');
            $table->unsignedBigInteger('eval_circular_section_id');

            $table->foreign('eval_circular_section_id')->references('id')
                ->on('eval_circular_sections')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_circular_indicators');
    }
};
