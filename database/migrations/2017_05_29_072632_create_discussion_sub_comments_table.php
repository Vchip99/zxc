<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionSubCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussion_sub_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discussion_post_id')->unsigned();
            $table->integer('discussion_comment_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('parent_id')->unsigned()->default(0);
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
        Schema::drop('discussion_sub_comments');
    }
}
