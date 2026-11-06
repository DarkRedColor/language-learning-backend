<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTheoriesJsonTheoryPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theories_json_theory', function (Blueprint $table) {
            $table->unsignedBigInteger('theories_json_id')->index();
            $table->foreign('theories_json_id')->references('id')->on('theories')->onDelete('cascade');
            $table->unsignedBigInteger('theory_id')->index();
            $table->foreign('theory_id')->references('id')->on('theories_jsons')->onDelete('cascade');
            $table->primary(['theories_json_id', 'theory_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('theories_json_theory');
    }
}
