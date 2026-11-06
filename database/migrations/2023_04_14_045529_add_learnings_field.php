<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLearningsField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_chapters', function (Blueprint $table) {
            $table->text('learnings')->nullable();
            $table->text('quiz_image')->nullable();
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
            $table->dropColumn('learnings');
            $table->dropColumn('quiz_image');
        });
    }
}
