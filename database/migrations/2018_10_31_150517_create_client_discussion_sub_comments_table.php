<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientDiscussionSubCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_discussion_sub_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_discussion_post_id')->unsigned();
            $table->integer('client_discussion_comment_id')->unsigned();
            $table->integer('clientuser_id')->unsigned();
            $table->integer('parent_id')->unsigned()->default(0);
            $table->text('body');
            $table->integer('client_id')->unsigned();
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
        Schema::connection('mysql2')->drop('client_discussion_sub_comments');
    }
}
