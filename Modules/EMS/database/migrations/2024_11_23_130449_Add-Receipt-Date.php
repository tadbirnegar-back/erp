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
        Schema::table('enactments', function (Blueprint $table) {
            $table->string('receipt_date')->nullable(); // Add the new column
        });
    }

    public function down()
    {
        Schema::table('enactments', function (Blueprint $table) {
            $table->dropColumn('receipt_date'); // Remove the column
        });
    }
};
