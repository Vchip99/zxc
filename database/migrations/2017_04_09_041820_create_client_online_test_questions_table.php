<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOnlineTestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_online_test_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('answer1');
            $table->text('answer2');
            $table->text('answer3');
            $table->text('answer4');
            $table->text('answer5');
            $table->text('answer6');
            $table->text('answer');
            $table->integer('category_id')->unsigned();
            $table->integer('subcat_id')->unsigned();
            $table->integer('section_type')->unsigned();
            $table->integer('question_type')->unsigned();
            $table->text('solution');
            $table->string('positive_marks');
            $table->string('negative_marks');
            $table->string('min');
            $table->string('max');
            $table->integer('subject_id')->unsigned();
            $table->integer('paper_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('client_institute_course_id')->unsigned();
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
        Schema::connection('mysql2')->drop('client_online_test_questions');
    }
}
