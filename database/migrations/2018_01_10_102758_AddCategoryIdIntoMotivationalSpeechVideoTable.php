<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdIntoMotivationalSpeechVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('motivational_speech_videos', function (Blueprint $table) {
            $table->integer('motivational_speech_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('motivational_speech_videos', function (Blueprint $table) {
           $table->dropColumn('motivational_speech_category_id');
        });
    }
}
