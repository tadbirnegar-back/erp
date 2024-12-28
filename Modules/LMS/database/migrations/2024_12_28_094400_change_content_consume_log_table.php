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
        Schema::table('content_consume_log', function (Blueprint $table) {
            $table->dateTime('created_date')->nullable();
            $table->boolean('is_complete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
