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
        Schema::table('odoc_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('ounit_id');
            $table->foreign('ounit_id')->references('id')->on('organization_units')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odoc_documents', function (Blueprint $table) {

        });
    }
};
