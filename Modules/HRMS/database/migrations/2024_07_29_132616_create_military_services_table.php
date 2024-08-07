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
        Schema::create('military_services', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('work_force_id');
            $table->unsignedBigInteger('exemption_type_id')->nullable();

            $table->unsignedBigInteger('military_service_status_id')->nullable();
            $table->dateTime('issue_date')->nullable();

            $table->foreign('work_force_id')->references('id')->on('work_forces')->onDelete('cascade');

            $table->foreign('exemption_type_id')->references('id')->on('exemption_types')->onDelete('cascade');

            $table->foreign('military_service_status_id')->references('id')->on('military_service_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('military_services');
    }
};
