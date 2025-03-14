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
        Schema::create('bnk_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('account_number')->index()->nullable();
            $table->string('iban_number')->index()->nullable();
            $table->unsignedBigInteger('account_type_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('ounit_id');
            $table->dateTime('register_date')->nullable();


            $table->foreign('branch_id')->references('id')->on('bnk_bank_branches')->onDelete('cascade');

            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnk_bank_accounts');
    }
};
