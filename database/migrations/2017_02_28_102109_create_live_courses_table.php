<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('category_id')->unsigned();
            $table->string('author');
            $table->text('author_introduction');
            $table->string('author_image');
            $table->string('image_path');
            $table->text('description');
            $table->integer('difficulty_level');
            $table->boolean('certified');
            $table->boolean('on_demand');
            $table->float('price', 8, 2);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('live_courses');
    }
}
