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

            $table->string('hierarchy_code')->nullable();
            $table->string('national_uid')->nullable();
            $table->string('abadi_code')->nullable();
            $table->string('ofc_code')->nullable();
            $table->string('population_1395')->nullable();
            $table->string('household_1395')->nullable();
            $table->boolean('isTourism')->nullable()->default(false);
            $table->boolean('isFarm')->nullable()->default(false);
            $table->boolean('isAttached_to_city')->nullable()->default(false);
            $table->boolean('hasLicense')->nullable()->default(false);
            $table->string('license_number')->nullable();
            $table->dateTime('license_date')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vilage_ofcs', function (Blueprint $table) {

        });
    }
};
