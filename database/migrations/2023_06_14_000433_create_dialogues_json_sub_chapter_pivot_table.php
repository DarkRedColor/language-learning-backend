<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDialoguesJsonSubChapterPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialogues_json_sub_chapter', function (Blueprint $table) {
            $table->unsignedBigInteger('dialogue_json_id')->index();
            $table->foreign('dialogue_json_id')->references('id')->on('dialogues_jsons')->onDelete('cascade');
            $table->unsignedBigInteger('sub_chapter_id')->index();
            $table->foreign('sub_chapter_id')->references('id')->on('sub_chapters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dialogues_json_sub_chapter');
    }
}
