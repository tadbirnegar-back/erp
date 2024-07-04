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
        Schema::create('script_approving_lists', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('script_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('priority');


            $table->foreign('script_id')->references('id')->on('recruitment_scripts')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_approving_lists');
    }
};
