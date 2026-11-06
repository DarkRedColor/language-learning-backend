<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('task')->nullable();
            $table->string('additional_task')->nullable();
            $table->string('answer')->nullable();
            $table->timestamps();

            $table->index(['name','task','additional_task']);
            $table->index(['answer']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_templates');
    }
}
