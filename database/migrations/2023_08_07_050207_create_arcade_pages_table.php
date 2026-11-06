<?php

use App\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArcadePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $languages = Language::all();
        Schema::create('arcade_pages', function (Blueprint $table) use ($languages) {
            $table->id();
            foreach ($languages as $language){
                $table->longText('text_'.$language->lcid)->nullable();
            }
            $table->integer('emotion_id')->nullable();
            $table->integer('position')->default(0);
            $table->integer('type');
            $table->integer('add_type')->nullable();
            $table->integer('order');
            $table->integer('parent_id')->nullable();
            $table->integer('variants')->nullable();
            $table->integer('place')->nullable();
            $table->integer('arcade_game_id');
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
        Schema::dropIfExists('arcade_pages');
    }
}
