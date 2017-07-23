<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVkitProjectSubCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vkit_project_sub_comment_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vkit_project_id')->unsigned();
            $table->integer('vkit_project_comment_id')->unsigned();
            $table->integer('vkit_project_sub_comment_id')->unsigned();
            $table->integer('user_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vkit_project_sub_comment_likes');
    }
}
