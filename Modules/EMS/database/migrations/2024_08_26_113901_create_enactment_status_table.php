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

        Schema::create('enactment_status', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('enactment_id');
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->unsignedBigInteger('status_id');

            $table->dateTime('create_date')->useCurrent();

            $table->foreign('enactment_id')->references('id')->on('enactments')->onDelete('cascade');
            $table->foreign('operator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enactment_status');
    }
};
