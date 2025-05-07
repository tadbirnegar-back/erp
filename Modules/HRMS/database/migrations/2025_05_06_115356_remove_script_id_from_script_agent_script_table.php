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
            $table->dropForeign(['script_id']);
            $table->dropColumn('script_id');
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
