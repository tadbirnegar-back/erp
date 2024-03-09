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
        Schema::create('status_work_force', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('work_force_id');
            $table->timestamp('create_date')->useCurrent();


            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('work_force_id')->references('id')->on('work_forces')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_work_force');
    }
};
