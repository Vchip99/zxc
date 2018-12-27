<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentor_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meeting_date');
            $table->string('from_time');
            $table->string('to_time');
            $table->integer('mentor_id');
            $table->integer('user_id');
            $table->string('comment');
            $table->integer('type');
            $table->tinyInteger('generated_by');
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
        Schema::drop('mentor_schedules');
    }
}
