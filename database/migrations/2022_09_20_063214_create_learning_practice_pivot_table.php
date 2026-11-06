<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLearningPracticePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_practice', function (Blueprint $table) {
            $table->unsignedBigInteger('learning_id')->index();
            $table->foreign('learning_id')->references('id')->on('learnings')->onDelete('cascade');
            $table->unsignedBigInteger('practice_id')->index();
            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('cascade');
            $table->primary(['learning_id', 'practice_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_practice');
    }
}
