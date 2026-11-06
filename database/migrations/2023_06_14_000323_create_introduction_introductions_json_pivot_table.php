<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIntroductionIntroductionsJsonPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('introduction_introductions_json', function (Blueprint $table) {
            $table->unsignedBigInteger('introduction_id')->index();
            $table->foreign('introduction_id')->references('id')->on('introductions')->onDelete('cascade');
            $table->unsignedBigInteger('json_id')->index();
            $table->foreign('json_id')->references('id')->on('introductions_jsons')->onDelete('cascade');
            $table->primary(['introduction_id', 'json_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('introduction_introductions_json');
    }
}
