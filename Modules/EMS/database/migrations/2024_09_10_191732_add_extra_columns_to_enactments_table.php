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
        Schema::table('enactment_status', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->unsignedBigInteger('attachment_id')->nullable();

            $table->foreign('attachment_id')->references('id')->on('files')->onDelete('set null');
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
