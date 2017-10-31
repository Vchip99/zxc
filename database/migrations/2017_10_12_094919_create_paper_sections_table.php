<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('duration');
            $table->integer('test_category_id')->unsigned();
            $table->integer('test_sub_category_id')->unsigned();
            $table->integer('test_subject_id')->unsigned();
            $table->integer('test_subject_paper_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('paper_sections');
    }
}
