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
        Schema::table('recruitment_scripts', function (Blueprint $table) {
            $table->text('description')->nullable()->after('position_id');
            $table->unsignedBigInteger('hire_type_id')->nullable()->after('position_id');
            $table->unsignedBigInteger('job_id')->nullable()->after('position_id');;
            $table->unsignedBigInteger('operator_id')->nullable()->after('position_id');
            $table->unsignedBigInteger('script_type_id')->nullable()->after('position_id');

            $table->dateTime('start_date')->nullable()->after('create_date');
            $table->dateTime('expire_date')->nullable();


            $table->foreign('hire_type_id')->references('id')->on('hire_types')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('operator_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('script_type_id')->references('id')->on('script_types')->onDelete('cascade')->onUpdate('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_scripts', function (Blueprint $table) {

        });
    }
};
