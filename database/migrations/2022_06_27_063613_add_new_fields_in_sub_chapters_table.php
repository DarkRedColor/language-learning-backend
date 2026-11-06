<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsInSubChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_chapters', function (Blueprint $table) {
            $table->string('locked_img')->nullable()->change();
            $table->renameColumn('locked_img','image_locked');
            $table->renameColumn('unlocked_img','image_unlocked');
            $table->integer('type')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_chapters', function (Blueprint $table) {
            $table->string('locked_img')->nullable(false)->change();
            $table->renameColumn('image_locked','locked_img');
            $table->renameColumn('image_unlocked','unlocked_img');
        });
    }
}
