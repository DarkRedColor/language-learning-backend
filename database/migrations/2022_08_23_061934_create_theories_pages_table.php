<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTheoriesPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theory_pages', function (Blueprint $table) {
            $table->id();
            $table->longText('text_en')->nullable();
            $table->text('image')->nullable();
            $table->integer('type')->unsigned();
            $table->integer('order')->default(0);
            $table->integer('theory_page_name_id');
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
        Schema::dropIfExists('theories_pages');
    }
}
