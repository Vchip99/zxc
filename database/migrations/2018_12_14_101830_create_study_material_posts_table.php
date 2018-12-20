<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyMaterialPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_material_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_category_id')->unsigned();
            $table->integer('course_sub_category_id')->unsigned();
            $table->integer('study_material_subject_id')->unsigned();
            $table->integer('study_material_topic_id')->unsigned();
            $table->text('body');
            $table->string('answer1');
            $table->string('answer2');
            $table->string('answer3');
            $table->string('answer4');
            $table->string('answer');
            $table->text('solution');
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
        Schema::drop('study_material_posts');
    }
}
