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
        Schema::create('evaluators', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            $table->id();
            $table->integer('sum')->nullable();
            $table->integer('average')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('parent_id')->references('id')->on($table->getTable())->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluators');
    }
};
