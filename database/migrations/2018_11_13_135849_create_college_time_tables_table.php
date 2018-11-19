<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeTimeTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_time_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('college_id');
            $table->integer('college_dept_id')->default(0);
            $table->integer('year')->default(0);
            $table->string('image_path');
            $table->integer('type');
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
        Schema::drop('college_time_tables');
    }
}
