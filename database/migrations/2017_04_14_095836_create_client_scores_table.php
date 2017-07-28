<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_user_id')->unsigned();
            $table->integer('client_institute_course_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('subcat_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->integer('paper_id')->unsigned();
            $table->integer('right_answered')->unsigned();
            $table->integer('wrong_answered')->unsigned();
            $table->integer('unanswered')->unsigned();
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
        Schema::connection('mysql2')->drop('client_scores');
    }
}
