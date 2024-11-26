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
        Schema::table('enactments', function (Blueprint $table) {

            $table->unsignedBigInteger('final_status_id')->nullable();
            $table->foreign('final_status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enactments', function (Blueprint $table) {

        });
    }
};
