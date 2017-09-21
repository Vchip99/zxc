<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('question');
            $table->integer('assignment_subject_id')->unsigned();
            $table->integer('assignment_topic_id')->unsigned();
            $table->string('attached_link');
            $table->integer('lecturer_id')->unsigned();
            $table->integer('college_id')->unsigned();
            $table->integer('college_dept_id')->unsigned();
            $table->integer('year')->unsigned();
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
        Schema::drop('assignment_questions');
    }
}
