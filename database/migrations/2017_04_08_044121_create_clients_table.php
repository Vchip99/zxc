<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('user_id');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('subdomain');
            $table->tinyInteger('verified')->default(0);
            $table->tinyInteger('admin_approve')->default(0);
            $table->tinyInteger('test_permission')->default(0);
            $table->tinyInteger('course_permission')->default(0);
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
        Schema::connection('mysql2')->drop('clients');
    }
}
