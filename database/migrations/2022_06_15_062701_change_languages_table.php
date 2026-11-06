<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->renameColumn('iso', "lcid");
            $table->string('iso_639_1')->nullable();
            $table->string('iso_639_2')->nullable();
            $table->renameColumn('title', "display_name");
//            $table->renameColumn('learn','my_language');
            $table->renameColumn('status', 'is_displayed_to_user');
            $table->renameColumn('icon', 'flag');
            $table->integer('deleted')->default(0);
        });
    }

//    /**
//     * Reverse the migrations.
//     *
//     * @return void
//     */
//    public function down()
//    {
//        Schema::table('languages', function (Blueprint $table) {
//
//        });
//    }
}
