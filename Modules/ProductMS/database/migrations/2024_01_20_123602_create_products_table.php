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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->fulltext()->index();
            $table->text('description');
            $table->string('sale_price')->nullable();
            $table->string('SKU')->nullable()->index();

            $table->integer('productable_id')->index();
            $table->string('productable_type')->index();
            $table->unsignedBigInteger('cover_file_id');
            $table->unsignedBigInteger('creator_id')->index();
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->unsignedBigInteger('unit_id')->index();

            $table->timestamp('create_date')->index()->useCurrent();

            $table->foreign('cover_file_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on($table->getTable())->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('cascade')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
