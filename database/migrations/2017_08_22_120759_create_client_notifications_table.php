<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message');
            $table->integer('client_id')->unsigned();
            $table->integer('notification_module')->unsigned();
            $table->integer('created_module_id')->unsigned();
            $table->integer('client_institute_course_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('created_to')->unsigned();
            $table->date('created_at');
            $table->tinyInteger('is_seen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->drop('client_notifications');
    }
}
