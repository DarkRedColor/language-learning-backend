<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->text('answer')->change();
            $table->text('wrong')->change();
            $table->text('mission_en')->change();
            $table->integer('type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('answer')->change();
            $table->string('wrong')->change();
            $table->string('mission_en')->change();
            $table->integer('type_id')->nullable(false)->change();
        });
    }
}
