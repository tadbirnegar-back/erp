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
        Schema::table('odoc_approvers', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable();

            $table->foreign('document_id')->references('id')->on('odoc_documents')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odoc_approvers', function (Blueprint $table) {

        });
    }
};
