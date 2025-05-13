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
        Schema::table('bgt_circular_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_type_id')->nullable()->index();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bgt_circular_subjects', function (Blueprint $table) {

        });
    }
};
