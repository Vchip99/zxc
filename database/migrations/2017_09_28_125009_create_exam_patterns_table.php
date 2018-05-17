<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamPatternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_patterns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('placement_area_id')->unsigned();
            $table->integer('placement_company_id')->unsigned();
            $table->string('testing_area');
            $table->string('no_of_question');
            $table->string('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('exam_patterns');
    }
}
