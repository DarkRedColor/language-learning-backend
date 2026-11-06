<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubChapterWordPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_chapter_word', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_chapter_id')->index();
            $table->foreign('sub_chapter_id')->references('id')->on('sub_chapters')->onDelete('cascade');
            $table->unsignedBigInteger('word_id')->index();
            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
//            $table->primary(['sub_chapter_id', 'word_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_chapter_word');
    }
}
