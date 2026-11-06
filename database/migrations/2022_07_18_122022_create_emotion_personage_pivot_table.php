<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmotionPersonagePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emotion_personage', function (Blueprint $table) {
            $table->unsignedBigInteger('emotion_id')->index();
            $table->foreign('emotion_id')->references('id')->on('emotions')->onDelete('cascade');
            $table->unsignedBigInteger('personage_id')->index();
            $table->foreign('personage_id')->references('id')->on('personages')->onDelete('cascade');
            $table->primary(['emotion_id', 'personage_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emotion_personage');
    }
}
