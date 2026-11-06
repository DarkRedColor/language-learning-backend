<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->text('description_en')->nullable();
            $table->string('mission_en')->nullable();
            $table->string('answer');
            $table->text('theory')->nullable();
            $table->integer('other_game_id')->nullable();
            $table->integer('chapter_id')->nullable();
            $table->timestamps();

            $table->index("name_en",'name_en',"BTREE");
            $table->index("chapter_id",'chapter_id',"BTREE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
