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
            $table->string('first_name')->index();
            $table->string('last_name');
            $table->string('mobile')->unique()->index();
            $table->string('phone_number')->nullable()->index();
            $table->string('father_name')->nullable()->index();
            $table->dateTime('birth_date');
            $table->string('job');

            $table->unsignedBigInteger('home_address_id')->index();
            $table->unsignedBigInteger('job_address_id')->index();

            $table->foreign('home_address_id')->references('id')->on('addresses')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('job_address_id')->references('id')->on('addresses')->onDelete('cascade')->onUpdate('cascade');
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
