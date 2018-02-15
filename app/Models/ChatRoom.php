<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\ChatMessage;
use DB;

class ChatRoom extends Model
{
    protected $fillable = ['room_type', 'user_ids'];

 	/**
 	* Get the messages of a chat room
 	*/
 	public function messages()
 	{
 		return $this->hasMany(ChatMessage::class, 'chat_room_id')->with('sender');
 	}
}
