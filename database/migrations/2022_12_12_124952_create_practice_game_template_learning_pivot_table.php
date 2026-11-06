<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePracticeGameTemplateLearningPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practice_game_template_learning', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('practice_id')->index();
            $table->unsignedBigInteger('learning_id')->index();
            $table->unsignedBigInteger('game_template_id')->index();
            $table->unsignedBigInteger('game_description_id')->index();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('practice_game_template_learning');
    }
}
