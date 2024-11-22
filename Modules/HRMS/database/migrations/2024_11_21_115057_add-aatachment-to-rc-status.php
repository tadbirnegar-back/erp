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
        Schema::table('recruitment_script_status', function (Blueprint $table) {
            $table->unsignedBigInteger('attachment_id')->nullable();

            $table->foreign('attachment_id')->references('id')->on('files');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_script_status', function (Blueprint $table) {

        });
    }
};
