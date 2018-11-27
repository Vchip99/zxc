<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegeMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('college_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('college_id');
            $table->string('college_dept_ids');
            $table->string('years');
            $table->string('photo');
            $table->text('message');
            $table->integer('created_by');
            $table->string('start_date');
            $table->string('end_date');
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
        Schema::drop('college_messages');
    }
}
