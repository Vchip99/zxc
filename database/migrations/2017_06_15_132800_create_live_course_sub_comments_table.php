<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveCourseSubCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_course_sub_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('live_course_video_id')->unsigned();
            $table->integer('live_course_comment_id')->unsigned();
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('user_id')->unsigned();
            $table->text('body');
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
        Schema::drop('live_course_sub_comments');
    }
}
