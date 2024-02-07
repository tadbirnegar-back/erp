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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->string('national_code')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->morphs('personable');
            $table->timestamp('create_date')->useCurrent();

            $table->unsignedBigInteger('profile_picture_id')->nullable();

            $table->foreign('profile_picture_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
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
