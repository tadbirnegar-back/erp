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
        Schema::create('sub_subscription', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date');
            $table->dateTime('expire_date');
            $table->unsignedBigInteger('ounit_id');

            $table->foreign('ounit_id')->references('id')->on('organization_units')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_subscription');
    }
};
