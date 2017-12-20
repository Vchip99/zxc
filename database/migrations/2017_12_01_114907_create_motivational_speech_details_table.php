<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotivationalSpeechDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motivational_speech_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('motivational_speech_category_id');
            $table->text('about');
            $table->string('about_image');
            $table->text('topics');
            $table->text('program_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('motivational_speech_details');
    }
}
