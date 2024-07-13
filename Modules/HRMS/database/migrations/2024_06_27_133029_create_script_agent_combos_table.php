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
        Schema::create('script_agent_combos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('default_value')->nullable();
            $table->text('formula')->nullable();
            $table->unsignedBigInteger('hire_type_id');
            $table->unsignedBigInteger('script_agent_id');
            $table->unsignedBigInteger('script_type_id');


            $table->foreign('hire_type_id')->references('id')->on('hire_types')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('script_agent_id')->references('id')->on('script_agents')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('script_type_id')->references('id')->on('script_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_agent_combos');
    }
};
