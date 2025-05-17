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
        Schema::table('bdm_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable()->change();

            $table->unsignedBigInteger('odoc_id')->nullable();
            $table->foreign('odoc_id')->references('id')->on('odoc_documents')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_id_nullable_in_bdm_forms');
    }
};
