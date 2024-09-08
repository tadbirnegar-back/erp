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
        
        Schema::create('attachmentables', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->index()->fulltext();
            $table->unsignedBigInteger('attachment_id');
            $table->morphs('attachmentable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachmentables');
    }
};
