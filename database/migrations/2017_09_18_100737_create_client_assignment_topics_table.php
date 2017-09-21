<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientAssignmentTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_assignment_topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('client_assignment_subject_id')->unsigned();
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
        Schema::connection('mysql2')->drop('client_assignment_topics');
    }
}
