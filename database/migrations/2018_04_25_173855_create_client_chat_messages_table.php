<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('client_chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_chat_room_id')->references('id')->on('client_chat_rooms');
            $table->integer('sender_id')->references('id')->on('clientusers');
            $table->integer('receiver_id')->references('id')->on('clientusers');
            $table->text('message');
            $table->tinyInteger('is_read');
            $table->integer('client_id');
            $table->tinyInteger('created_by_client');
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
        Schema::connection('mysql2')->drop('client_chat_messages');
    }
}
