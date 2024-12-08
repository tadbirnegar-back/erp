<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('checks', function (Blueprint $table) {
            // Adding a new unsignedBigInteger column for the foreign key
            $table->unsignedBigInteger('check_file_id')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('serial_number')->nullable();
        });
    }

    public function down()
    {
        Schema::table('checks', function (Blueprint $table) {
            // Dropping the column in case of rollback
            $table->dropColumn('receipt_file_id');
        });
    }
};
