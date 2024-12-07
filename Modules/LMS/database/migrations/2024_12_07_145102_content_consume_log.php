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
        Schema::create('content_consume_log', function (Blueprint $table) {
            $table->id();
            $table->longText('consume_data');
            $table->integer('consume_round');
            $table->unsignedBigInteger('content_id');
            $table->dateTime('craete_date');
            $table->dateTime('last_modified');
            $table->unsignedBigInteger('student_id');


            $table->foreign('student_id')->references('id')->on('students')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('content_id')->references('id')->on('contents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
