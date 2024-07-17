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
        Schema::create('confirmation_type_script_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('confirmation_type_id');
            $table->unsignedBigInteger('script_type_id');
            $table->unsignedBigInteger('option_id')->nullable();
            $table->unsignedBigInteger('priority')->nullable();


            $table->foreign('confirmation_type_id')->references('id')->on('confirmation_types')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('script_type_id')->references('id')->on('script_types')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confirmation_type_script_type');
    }
};
