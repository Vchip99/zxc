<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->text('answer');
            $table->integer('assignment_question_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->integer('lecturer_id')->unsigned();
            $table->string('attached_link');
            $table->tinyInteger('is_student_created')->unsigned();
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
        Schema::drop('assignment_answers');
    }
}
