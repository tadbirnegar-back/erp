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
        Schema::table('acc_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->nullable()->index();
            $table->string('entity_type')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acc_accounts', function (Blueprint $table) {

        });
    }
};
