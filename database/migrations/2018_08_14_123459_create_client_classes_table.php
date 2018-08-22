<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_batch_id');
            $table->integer('clientuser_id');
            $table->string('subject');
            $table->string('topic');
            $table->string('date');
            $table->string('from_time');
            $table->string('to_time');
            $table->integer('client_id');
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
        Schema::connection('mysql2')->drop('client_classes');
    }
}
