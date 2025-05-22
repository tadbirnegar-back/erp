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
        Schema::create('bdm_report_data_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('report_item_id');

            $table->foreign('report_id')->references('id')
                ->on('bdm_reports')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('report_item_id')->references('id')
                ->on('bdm_report_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdm_report_data_items');
    }
};
