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
        Schema::table('script_types', function (Blueprint $table) {
            $table->unsignedBigInteger('origin_id')->nullable()->after('issue_time_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('script_types', function (Blueprint $table) {

        });
    }
};
