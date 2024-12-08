<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('create_date');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('description');
            $table->morphs('orderable');
            $table->integer('requested_invoice_count');
            $table->float('total_price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
