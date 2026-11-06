<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubChapterSummariesJsonPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_chapter_summaries_json', function (Blueprint $table) {
            $table->unsignedBigInteger('scene_id')->index();
            $table->foreign('scene_id','scene_id')->references('id')->on('sub_chapters')->onDelete('cascade');
            $table->unsignedBigInteger('json_id')->index();
            $table->foreign('json_id','json_id')->references('id')->on('summaries_jsons')->onDelete('cascade');
            $table->primary(['scene_id', 'json_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_chapter_summaries_json');
    }
}
