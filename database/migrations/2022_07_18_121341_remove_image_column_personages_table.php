<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveImageColumnPersonagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personages', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->renameColumn('name','name_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personages', function (Blueprint $table) {
            $table->text('image')->nullable();
            $table->renameColumn('name_en','name');

        });
    }
}
