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
        Schema::disableForeignKeyConstraints();
        Schema::create('script_agent_script', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('contract');
            $table->unsignedBigInteger('script_id');
            $table->unsignedBigInteger('script_agent_id');

            $table->foreign('script_id')->references('id')->on('recruitment_scripts')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('script_agent_id')->references('id')->on('script_agents')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_agent_script');
    }
};
