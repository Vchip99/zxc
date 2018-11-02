<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientDiscussionPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_discussion_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clientuser_id')->unsigned();
            $table->integer('client_discussion_category_id')->unsigned();
            $table->string('title');
            $table->text('body');
            $table->string('answer1');
            $table->string('answer2');
            $table->string('answer3');
            $table->string('answer4');
            $table->string('answer');
            $table->text('solution');
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
        Schema::connection('mysql2')->drop('client_discussion_posts');
    }
}
