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
        Schema::create('bgt_circular_subjects', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('isActive')->default(true);

            $table->unsignedBigInteger('old_item_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->dateTime('create_date')->useCurrent();

            $table->foreign('parent_id')->references('id')->on($table->getTable())->onDelete('set null');

            $table->foreign('old_item_id')->references('id')->on('bgt_circular_items')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bgt_circular_subjects');
    }
};
