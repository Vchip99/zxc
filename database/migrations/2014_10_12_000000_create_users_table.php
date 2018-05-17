<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->integer('user_type');
            $table->tinyInteger('verified')->default(0);
            $table->tinyInteger('admin_approve')->default(0);
            $table->integer('degree');
            $table->string('college_id');
            $table->integer('college_dept_id');
            $table->integer('year');
            $table->integer('roll_no');
            $table->string('other_source')->nullable();
            $table->string('photo')->nullable();
            $table->string('resume')->nullable();
            $table->string('recorded_video')->nullable();
            $table->string('email_token')->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
