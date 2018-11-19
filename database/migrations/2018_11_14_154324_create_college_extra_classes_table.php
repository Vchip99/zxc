<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeExtraClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_extra_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('college_id');
            $table->string('college_dept_ids');
            $table->string('years');
            $table->integer('college_subject_id');
            $table->integer('created_by');
            $table->string('topic');
            $table->string('date');
            $table->string('from_time');
            $table->string('to_time');
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
        Schema::drop('college_extra_classes');
    }
}
