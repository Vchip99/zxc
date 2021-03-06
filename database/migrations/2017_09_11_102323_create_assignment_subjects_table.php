<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('lecturer_id')->unsigned();
            $table->integer('college_id')->unsigned();
            $table->integer('college_dept_id')->unsigned();
            $table->integer('year')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assignment_subjects');
    }
}
