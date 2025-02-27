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
        Schema::create('eval_circulars', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->string('title')->fulltext();
            $table->unsignedBigInteger('file_id');
            $table->boolean('is_optional')->default(false);
            $table->integer('maximum_value');
            $table->unsignedBigInteger('creator_id');
            $table->dateTime('create_date')->nullable();
            $table->dateTime('expired_date')->nullable();

            $table->foreign('file_id')->references('id')
                ->on('files')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('creator_id')->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_circulars');
    }
};
