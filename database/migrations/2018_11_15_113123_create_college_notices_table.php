<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_notices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('college_id');
            $table->string('college_dept_ids');
            $table->string('years');
            $table->string('date');
            $table->integer('created_by');
            $table->string('notice');
            $table->tinyInteger('is_emergency');
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
        Schema::drop('college_notices');
    }
}
