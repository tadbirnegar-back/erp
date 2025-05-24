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
        Schema::table('bdm_reports', function (Blueprint $table) {
            $table->dropForeign('bdm_reports_file_id_foreign');
            $table->dropColumn('file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('file_id_from_bdm_reports', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
        });
    }
};
