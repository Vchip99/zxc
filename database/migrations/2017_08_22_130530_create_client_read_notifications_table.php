<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientReadNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_read_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_notification_id')->unsigned();
            $table->integer('notification_module')->unsigned();
            $table->integer('created_module_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('client_user_id')->unsigned();
            $table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->drop('client_read_notifications');
    }
}
