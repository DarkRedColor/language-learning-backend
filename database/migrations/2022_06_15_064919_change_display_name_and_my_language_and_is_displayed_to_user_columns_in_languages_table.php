<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDisplayNameAndMyLanguageAndIsDisplayedToUserColumnsInLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('languages', function (Blueprint $table) {

            $table->string('display_name')->nullable()->change();
            $table->text('my_language')->nullable()->change();
            $table->boolean('is_displayed_to_user')->default(0)->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('languages', function (Blueprint $table) {

            $table->string('display_name')->change();
            $table->text('my_language')->change();
            $table->boolean('is_displayed_to_user')->change();

        });
    }
}
