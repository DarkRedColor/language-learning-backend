<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intros', function (Blueprint $table) {
            $table->id();
            $table->integer('current')->nullable();
            $table->integer('position')->nullable();
            $table->integer('personage_id')->nullable();
            $table->integer('emotion_id')->nullable();
            $table->string('text_en')->nullable();
            $table->string('text_ru')->nullable();
            $table->string('next_id')->nullable();
            $table->integer('sub_chapter_id');
            $table->integer('background_id')->nullable();
            $table->integer('content_type');
            $table->integer('parent_id')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();

            $table->index('background_id');
            $table->index('emotion_id');
            $table->index('next_id');
            $table->index('personage_id');
            $table->index('text_en');
            $table->index('parent_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intros');
    }
}
