<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_solutions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ques_id')->unsigned();
            $table->string('ques_answer');
            $table->string('user_answer');
            $table->integer('user_id')->unsigned();
            $table->integer('paper_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->integer('score_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_solutions');
    }
}
