<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('course_category_id')->unsigned();
            $table->integer('course_sub_category_id')->unsigned();
            $table->string('author');
            $table->text('author_introduction');
            $table->string('author_image');
            $table->text('description');
            $table->float('price', 8, 2);
            $table->integer('difficulty_level')->unsigned();
            $table->boolean('certified');
            $table->string('image_path');
            $table->dateTime('release_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_courses');
    }
}
