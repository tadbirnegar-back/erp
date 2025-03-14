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
        Schema::create('acc_documents', function (Blueprint $table) {
            $table->id();

            $table->string('document_number', 255)->index()->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('fiscal_year_id')->index();
            $table->unsignedBigInteger('creator_id')->index();
            $table->unsignedBigInteger('ounit_id')->index();
            $table->dateTime('document_date')->nullable();
            $table->dateTime('create_date')->useCurrent();

            $table->foreign('ounit_id')->references('id')->on('organization_units');
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_documents');
    }
};
