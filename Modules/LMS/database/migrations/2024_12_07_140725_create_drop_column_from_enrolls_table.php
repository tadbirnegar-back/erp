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
        Schema::table('enrolls', function (Blueprint $table) {
            $table->dropColumn('expired_time');
            $table->unsignedBigInteger('certificate_file_id')->nullable();
            $table->boolean('study_completed')->default(false);
            $table->integer('study_count');
            $table->foreign('certificate_file_id')->references('id')->on('files')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
        });
    }
};
