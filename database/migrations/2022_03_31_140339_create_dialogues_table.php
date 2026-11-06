<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDialoguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialogues', function (Blueprint $table) {
            $table->id();
            $table->integer('current')->nullable();
            $table->integer('position')->nullable();
            $table->text('background')->nullable();
            $table->integer('personage_id')->nullable();
            $table->integer('emotion_id')->nullable();
            $table->text('text_en');
            $table->text('text_ru')->nullable();
            $table->text('text_de')->nullable();
            $table->text('text_es')->nullable();
            $table->text('text_pt')->nullable();
            $table->text('text_mx')->nullable();
            $table->text('text_br')->nullable();
            $table->integer('order')->nullable();
            $table->integer('sub_chapter_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dialogues');
    }
}
