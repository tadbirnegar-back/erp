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
        Schema::table('naturals', function (Blueprint $table) {

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('naturals', function (Blueprint $table) {
            $table->dateTime('bc_issue_date')->nullable();
            $table->string('bc_issue_location')->nullable();
            $table->string('bc_serial')->nullable();
            $table->unsignedBigInteger('religion_id')->nullable();
            $table->unsignedBigInteger('religion_type_id')->nullable();
            $table->dropColumn('military_service_status_id');

            $table->foreign('religion_id')->references('id')->on('religions')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('religion_type_id')->references('id')->on('religion_types')->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
