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

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('meeting_detail')->nullable();
            $table->integer('meeting_number')->nullable();
            $table->boolean('isTemplate')->default(false);
            $table->text('summary')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('meeting_type_id');
            $table->unsignedBigInteger('ounit_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->dateTime('create_date');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->dateTime('invitation_date')->nullable();
            $table->dateTime('meeting_date')->nullable();
            $table->dateTime('reminder_date')->nullable();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('meeting_type_id')->references('id')->on('meeting_types')->onDelete('cascade');
            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on($table->getTable())->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
