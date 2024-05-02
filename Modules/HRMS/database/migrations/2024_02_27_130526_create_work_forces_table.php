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
        Schema::create('work_forces', function (Blueprint $table) {
            $table->id();
            $table->morphs('workforceable');
            $table->boolean('isMarried');
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('military_service_status_id')->nullable();


            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('military_service_status_id')->references('id')->on('military_service_statuses')->onDelete('cascade')->onUpdate('cascade');

//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_forces');
    }
};
