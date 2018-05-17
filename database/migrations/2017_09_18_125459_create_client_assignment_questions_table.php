<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientAssignmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_assignment_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('question');
            $table->integer('client_assignment_subject_id')->unsigned();
            $table->integer('client_assignment_topic_id')->unsigned();
            $table->string('attached_link');
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
        Schema::connection('mysql2')->drop('client_assignment_questions');
    }
}