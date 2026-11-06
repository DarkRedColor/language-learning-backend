<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesJsonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $languages = \App\Models\Language::all();
        Schema::create('games_jsons', function (Blueprint $table) use ($languages) {
            $table->id();
            foreach ($languages as $language){
                $table->longText($language->lcid)->nullable();
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games_jsons');
    }
}
