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
        Schema::create('dependents', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();

            $table->id();
            $table->unsignedBigInteger('main_person_id');
            $table->unsignedBigInteger('related_person_id');
            $table->unsignedBigInteger('relation_type_id');


            $table->foreign('main_person_id')->references('id')->on('persons')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('related_person_id')->references('id')->on('persons')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('relation_type_id')->references('id')->on('relative_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependents');
    }
};
