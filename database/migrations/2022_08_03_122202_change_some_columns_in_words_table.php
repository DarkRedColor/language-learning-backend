<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSomeColumnsInWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->text('image')->nullable();
            $table->text('definition_en')->nullable();
            $table->text('example_list')->nullable();
            $table->string('transcription')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('definition_en');
            $table->dropColumn('example_list');
            $table->string('transcription')->nullable(false)->change();
        });
    }
}
