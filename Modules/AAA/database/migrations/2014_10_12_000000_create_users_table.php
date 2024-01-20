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
        Schema::disableForeignKeyConstraints();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
//            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('email')->unique();
            $table->string('username')->unique();
//            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('person_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('person_id');
        });
        Schema::dropIfExists('users');
    }
};
