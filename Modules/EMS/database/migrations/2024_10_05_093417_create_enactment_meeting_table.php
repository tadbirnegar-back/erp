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
        Schema::create('enactment_meeting', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('enactment_id');
            $table->unsignedBigInteger('meeting_id');
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('enactment_id')->references('id')->on('enactments')->onDelete('cascade');
            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enactment_meeting');
    }
};
