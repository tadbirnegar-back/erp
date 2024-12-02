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
        Schema::create('ounit_cat_properties', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');
            $table->string('name');
            $table->unsignedBigInteger('ounit_cat_id');
            $table->unsignedBigInteger('predefined_cat_id');

            $table->foreign('predefined_cat_id')->references('id')
                ->on('ounit_cat_predefined_values')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ounit_cat_properties');
    }
};
