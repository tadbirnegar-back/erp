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
        Schema::create('hrmContract_status', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->dateTime('create_date')->useCurrent();


            $table->foreign('contract_id')->references('id')->on('hrm_contracts')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrmContract_status');
    }
};
