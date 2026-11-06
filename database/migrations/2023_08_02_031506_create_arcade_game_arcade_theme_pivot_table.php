<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArcadeGameArcadeThemePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arcade_game_arcade_theme', function (Blueprint $table) {
            $table->unsignedBigInteger('arcade_game_id')->index();
            $table->foreign('arcade_game_id')->references('id')->on('arcade_games')->onDelete('cascade');
            $table->unsignedBigInteger('arcade_theme_id')->index();
            $table->foreign('arcade_theme_id')->references('id')->on('arcade_themes')->onDelete('cascade');
            $table->primary(['arcade_game_id', 'arcade_theme_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arcade_game_arcade_theme');
    }
}
