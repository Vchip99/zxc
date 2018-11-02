<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientDiscussionLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_discussion_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_discussion_post_id');
            $table->integer('client_discussion_comment_id');
            $table->integer('client_discussion_sub_comment_id');
            $table->integer('clientuser_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->tinyInteger('created_by');
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
        Schema::connection('mysql2')->drop('client_discussion_likes');
    }
}
