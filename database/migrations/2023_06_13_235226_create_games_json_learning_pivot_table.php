<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGamesJsonLearningPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games_json_learning', function (Blueprint $table) {
            $table->unsignedBigInteger('games_json_id')->index();
            $table->foreign('games_json_id')->references('id')->on('games_jsons')->onDelete('cascade');
            $table->unsignedBigInteger('learning_id')->index();
            $table->foreign('learning_id')->references('id')->on('learnings')->onDelete('cascade');
            $table->primary(['games_json_id', 'learning_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_json_learning');
    }
}
