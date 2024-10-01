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
        Schema::disableForeignKeyConstraints();
        Schema::create('enactments', function (Blueprint $table) {
            $table->id();
            $table->string('custom_title')->nullable()->fulltext();
            $table->text('description')->nullable();
            $table->string('auto_serial')->nullable()->index();
            $table->string('serial')->nullable()->index();

            $table->unsignedBigInteger('title_id')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('meeting_id')->nullable();
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('title_id')->references('id')->on('enactment_titles')->onDelete('set null');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('set null');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enactments');
    }
};
