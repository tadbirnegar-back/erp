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
        Schema::disableForeignKeyConstraints();

        Schema::create('enactment_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('attachment_id')->nullable();
            $table->unsignedBigInteger('enactment_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('user_id');

            $table->dateTime('create_date')->useCurrent();
            $table->dateTime('expired_date')->nullable();

            $table->foreign('attachment_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('enactment_id')->references('id')->on('enactments')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enactment_reviews');
    }
};
