<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMotivationMotivationsJsonPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motivation_motivations_json', function (Blueprint $table) {
            $table->unsignedBigInteger('motivation_id')->index();
            $table->foreign('motivation_id')->references('id')->on('motivations')->onDelete('cascade');
            $table->unsignedBigInteger('json_id')->index();
            $table->foreign('json_id')->references('id')->on('motivations_jsons')->onDelete('cascade');
            $table->primary(['motivation_id', 'json_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motivation_motivations_json');
    }
}
