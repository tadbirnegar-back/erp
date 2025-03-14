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
        Schema::create('bnk_bank_branches', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('branch_code')->index();
            $table->text('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->unsignedBigInteger('bank_id');

            $table->foreign('bank_id')->references('id')->on('bnk_banks');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bnk_bank_branches');
    }
};
