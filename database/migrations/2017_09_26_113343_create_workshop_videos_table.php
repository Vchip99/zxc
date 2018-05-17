<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkshopVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workshop_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('workshop_category_id')->unsigned();
            $table->integer('workshop_details_id')->unsigned();
            $table->text('description');
            $table->string('duration');
            $table->string('video_path');
            $table->dateTime('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('workshop_videos');
    }
}
