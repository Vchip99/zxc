<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussion_comment_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discussion_post_id')->unsigned();
            $table->integer('discussion_comment_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('is_like')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('discussion_comment_likes');
    }
}
