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
        Schema::table('bdm_estate_utm', function (Blueprint $table) {
            $table->dropForeign('bdm_estate_utm_dossier_id_foreign');
            $table->dropColumn('dossier_id');
            $table->unsignedBigInteger('estate_id');
            $table->foreign('estate_id')->references('id')->on('bdm_estates')->onDelete('cascade')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
