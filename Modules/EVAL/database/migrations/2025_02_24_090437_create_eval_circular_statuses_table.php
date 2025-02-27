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
        Schema::create('eval_circular_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id');

            $table->foreign('status_id')->references('id')
                ->on('statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('eval_circular_id');
            $table->foreign('eval_circular_id')->references('id')
                ->on('eval_circulars')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_circular_statuses');
    }
};
