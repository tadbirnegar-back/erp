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
        Schema::disableForeignKeyConstraints();
        Schema::create('acc_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name')->fulltext()->index();
            $table->string('segment_code')->index();
            $table->string('chain_code')->index();
            $table->morphs('accountable');
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('ounit_id')->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->unsignedBigInteger('status_id')->index();

            $table->foreign('parent_id')->references('id')->on('acc_accounts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('acc_account_categories')->onDelete('cascade');
            $table->foreign('ounit_id')->references('id')->on('organization_units')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('bgt_circular_subjects')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_accounts');
    }
};
