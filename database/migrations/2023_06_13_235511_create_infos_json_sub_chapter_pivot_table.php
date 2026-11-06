<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInfosJsonSubChapterPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infos_json_sub_chapter', function (Blueprint $table) {
            $table->unsignedBigInteger('info_json_id')->index();
            $table->foreign('info_json_id')->references('id')->on('infos_jsons')->onDelete('cascade');
            $table->unsignedBigInteger('sub_chapter_id')->index();
            $table->foreign('sub_chapter_id')->references('id')->on('sub_chapters')->onDelete('cascade');
            $table->primary(['info_json_id', 'sub_chapter_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infos_json_sub_chapter');
    }
}
