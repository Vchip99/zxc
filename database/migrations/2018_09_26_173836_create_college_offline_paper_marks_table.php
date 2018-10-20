<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeOfflinePaperMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_offline_paper_marks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('college_id');
            $table->integer('college_subject_id');
            $table->integer('college_offline_paper_id');
            $table->integer('user_id');
            $table->string('marks');
            $table->string('total_marks');
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
        Schema::drop('college_offline_paper_marks');
    }
}