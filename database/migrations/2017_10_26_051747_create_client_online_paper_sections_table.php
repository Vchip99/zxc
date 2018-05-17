<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOnlinePaperSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_online_paper_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('duration');
            $table->integer('category_id')->unsigned();
            $table->integer('sub_category_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->integer('paper_id')->unsigned();
            $table->integer('client_id');
            $table->integer('client_institute_course_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->drop('client_online_paper_sections');
    }
}
