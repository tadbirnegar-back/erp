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

        Schema::create('meeting_agendas', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable()->index()->fulltext();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('meeting_id');

            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_agendas');
    }
};
