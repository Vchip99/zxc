<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogSubCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_sub_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blog_id')->unsigned();
            $table->integer('blog_comment_id')->unsigned();
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
        Schema::drop('blog_sub_comments');
    }
}
