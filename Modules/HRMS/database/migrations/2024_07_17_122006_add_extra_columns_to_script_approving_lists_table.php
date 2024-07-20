<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('script_approving_lists', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();

            $table->dropForeign('script_approving_lists_employee_id_foreign');
            $table->dropColumn('employee_id');

            $table->unsignedBigInteger('approver_id')->nullable();

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->dateTime('create_date')->nullable();
            $table->dateTime('update_date')->nullable();

            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('script_approving_lists', function (Blueprint $table) {

        });
    }
};
