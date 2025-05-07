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
        Schema::table('script_agent_script', function (Blueprint $table) {

            $table->unsignedBigInteger('contract_id');

            $table->foreign('contract_id')->references('id')->on('hrm_contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('script_agent_script', function (Blueprint $table) {

        });
    }
};
