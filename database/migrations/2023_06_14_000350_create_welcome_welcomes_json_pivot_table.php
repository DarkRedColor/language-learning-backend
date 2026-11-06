<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWelcomeWelcomesJsonPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('welcome_welcomes_json', function (Blueprint $table) {
            $table->unsignedBigInteger('welcome_id')->index();
            $table->foreign('welcome_id')->references('id')->on('welcomes')->onDelete('cascade');
            $table->unsignedBigInteger('welcomes_json_id')->index();
            $table->foreign('welcomes_json_id')->references('id')->on('welcomes_jsons')->onDelete('cascade');
            $table->primary(['welcome_id', 'welcomes_json_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('welcome_welcomes_json');
    }
}
