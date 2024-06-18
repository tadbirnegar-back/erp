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
        Schema::create('script_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('issue_time_id')->nullable();
            $table->unsignedBigInteger('employee_status_id')->nullable()
                ->comment('the status employee should be set aftergetting this type of recruitment script');

            $table->foreign('issue_time_id')->references('id')->on('issue_times')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_types');
    }
};
