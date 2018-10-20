<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeUserAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_user_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attendance_date');
            $table->integer('college_id');
            $table->integer('college_dept_id');
            $table->integer('year');
            $table->integer('college_subject_id');
            $table->string('student_ids');
            $table->integer('created_by');
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
        Schema::drop('college_user_attendances');
    }
}
