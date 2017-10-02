<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placement_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('placement_area_id')->unsigned();
            $table->integer('placement_company_id')->unsigned();
            $table->text('selection_process');
            $table->text('academic_criteria');
            $table->text('aptitude_syllabus');
            $table->text('hr_questions');
            $table->string('job_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('placement_processes');
    }
}
