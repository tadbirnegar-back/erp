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
        Schema::create('file_script', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('script_id');
            $table->unsignedBigInteger('file_id');

            $table->foreign('script_id')->references('id')->on('recruitment_scripts')->onDelete('cascade');
            
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_script');
    }
};
