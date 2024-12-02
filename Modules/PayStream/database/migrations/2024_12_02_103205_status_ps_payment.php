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
        Schema::create('status_ps_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('payment_id');
            $table->dateTime('create_date')->nullable();
            $table->unsignedBigInteger('creator_id');

            $table->foreign('status_id')->references('id')
                ->on('statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('payment_id')->references('id')
                ->on('ps_payment')
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
        Schema::dropIfExists('status_ps_payment');
    }
};
