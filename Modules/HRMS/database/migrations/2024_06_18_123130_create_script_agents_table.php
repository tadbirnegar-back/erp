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
        Schema::create('script_agents', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->unsignedBigInteger('script_agent_type_id');


            $table->foreign('script_agent_type_id')->references('id')->on('script_agent_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_agents');
    }
};
