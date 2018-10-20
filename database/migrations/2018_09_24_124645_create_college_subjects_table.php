<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('college_id');
            $table->string('college_dept_ids');
            $table->string('years');
            $table->integer('lecturer_id');
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
        Schema::drop('college_subjects');
    }
}
