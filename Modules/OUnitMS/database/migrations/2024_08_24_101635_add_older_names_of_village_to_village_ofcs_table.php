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
        Schema::table('village_ofcs', function (Blueprint $table) {
            $table->string('village_name_in_85')->nullable();
            $table->string('village_name_in_90')->nullable();
            $table->string('village_name_in_95')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('village_ofcs', function (Blueprint $table) {

        });
    }
};
