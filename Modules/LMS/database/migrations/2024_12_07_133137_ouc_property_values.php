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
        Schema::create('ouc_property_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ouc_property_id');
            $table->string('value');

            $table->foreign('ouc_property_id')->references('id')->on('ouc_properties')->onUpdate('cascade')->onDelete('cascade');
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
