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
        Schema::create('bdm_estates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ounit_id');
            $table->integer('ownership_type_id')->nullable();
            $table->longText('part')->nullable();
            $table->integer('transfer_type_id')->nullable();
            $table->longText('postal_code')->nullable();
            $table->longText('address')->nullable();
            $table->longText('ounit_number')->nullable();
            $table->longText('main')->nullable();
            $table->longText('minor')->nullable();
            $table->longText('deal_number')->nullable();
            $table->longText('building_number')->nullable();
            $table->unsignedBigInteger('dossier_id');
            $table->unsignedBigInteger('app_id');
            $table->float('area' , 11 , 2)->nullable();
            $table->dateTime('created_date');
            $table->dateTime('request_date');


            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('app_id')->references('id')->on('pfm_prop_applications')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dossier_id')->references('id')->on('bdm_building_dossiers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('');
    }
};
