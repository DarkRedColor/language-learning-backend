<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLearningTheoryPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_theory', function (Blueprint $table) {
            $table->unsignedBigInteger('learning_id')->index();
            $table->foreign('learning_id')->references('id')->on('learnings')->onDelete('cascade');
            $table->unsignedBigInteger('theory_id')->index();
            $table->foreign('theory_id')->references('id')->on('theories')->onDelete('cascade');
            $table->primary(['learning_id', 'theory_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_theory');
    }
}
