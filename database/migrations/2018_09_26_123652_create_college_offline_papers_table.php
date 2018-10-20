<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeOfflinePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_offline_papers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('college_id');
            $table->integer('college_dept_id');
            $table->integer('college_subject_id');
            $table->integer('year');
            $table->string('marks');
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
        Schema::drop('college_offline_papers');
    }
}
