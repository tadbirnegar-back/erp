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
        Schema::create('ouc_properties', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->string('name');
            $table->unsignedBigInteger('ounit_cat_id');

            $table->foreign('ounit_cat_id')->references('id')->on('ounit_cats')->onDelete('cascade')->onUpdate('cascade');
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
