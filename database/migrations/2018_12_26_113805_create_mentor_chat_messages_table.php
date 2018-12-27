<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMentorChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentor_chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mentor_chat_room_id')->references('id')->on('mentor_chat_rooms');
            $table->integer('sender_id');
            $table->integer('receiver_id');
            $table->text('message');
            $table->tinyInteger('is_read');
            $table->tinyInteger('generated_by_mentor');
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
        Schema::drop('mentor_chat_messages');
    }
}
