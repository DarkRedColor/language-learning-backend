<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTheoryWordPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theory_word', function (Blueprint $table) {
            $table->unsignedBigInteger('theory_id')->index();
            $table->foreign('theory_id')->references('id')->on('theories')->onDelete('cascade');
            $table->unsignedBigInteger('word_id')->index();
            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
            $table->primary(['theory_id', 'word_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theory_word');
    }
}
