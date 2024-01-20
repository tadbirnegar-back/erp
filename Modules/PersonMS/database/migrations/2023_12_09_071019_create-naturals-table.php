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
        Schema::create('naturals', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->index()->fulltext();
            $table->string('last_name')->index()->fulltext();
            $table->string('mobile')->unique()->index();
            $table->string('phone_number')->nullable()->index();
            $table->string('father_name')->nullable()->index();
            $table->dateTime('birth_date');
            $table->string('job');
            $table->boolean('isMarried')->index()->nullable();
            $table->string('level_of_spouse_education')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_last_name')->nullable();

            $table->unsignedBigInteger('home_address_id')->index();
            $table->unsignedBigInteger('job_address_id')->index();
            $table->unsignedBigInteger('gender_id')->index();
            $table->unsignedBigInteger('military_service_status_id')->index()->nullable();

            $table->foreign('home_address_id')->references('id')->on('addresses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('job_address_id')->references('id')->on('addresses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('military_service_status_id')->references('id')->on('military_service_statuses')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
