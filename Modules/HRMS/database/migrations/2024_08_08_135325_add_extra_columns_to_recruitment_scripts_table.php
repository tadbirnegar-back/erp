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
        Schema::table('recruitment_scripts', function (Blueprint $table) {
            $table->unsignedBigInteger('enactment_attachment_id')->nullable();
            $table->unsignedBigInteger('script_attachment_id')->nullable();


            $table->foreign('enactment_attachment_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('script_attachment_id')->references('id')->on('files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_scripts', function (Blueprint $table) {

        });
    }
};
