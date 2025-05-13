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
        Schema::create('bill_item_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_tariff_id');
            $table->longText('key');
            $table->longText('value');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_item_properties_table');
    }
};
