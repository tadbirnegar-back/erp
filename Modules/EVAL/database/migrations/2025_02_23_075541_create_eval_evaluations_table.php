
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
        Schema::create('eval_evaluations', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->string('title');
            $table->unsignedBigInteger('eval_circular_id');
            $table->unsignedBigInteger('evaluator_id')->nullable();
            $table->unsignedBigInteger('target_ounit_id');
            $table->unsignedBigInteger('evaluator_ounit_id')->nullable();
            $table->dateTime('create_date');
            $table->float('sum')->nullable();
            $table->unsignedBigInteger('creator_id');
            $table->boolean('is_revised')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->float('average')->nullable();

            $table->foreign('eval_circular_id')->references('id')
                ->on('eval_circulars')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('target_ounit_id')->references('id')
                ->on('organization_units')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('creator_id')->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('parent_id')->references('id')
                ->on('eval_evaluations')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('evaluator_id')->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('evaluator_ounit_id')->references('id')
                ->on('organization_units')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eval_evaluations');
    }
};
