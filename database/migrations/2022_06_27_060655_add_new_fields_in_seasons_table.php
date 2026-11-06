<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsInSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
            $table->renameColumn('image', 'image_locked');
            $table->string('image_unlocked')->nullable();
            $table->string('background')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
            $table->renameColumn('image_locked','image');
            $table->dropColumn('image_unlocked');
            $table->dropColumn('background');
        });
    }
}
