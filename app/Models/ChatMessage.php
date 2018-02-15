<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\User;
use App\Models\ChatRoom;
use DB,Auth,Cache;

class ChatMessage extends Model
{
    protected $fillable = ['chat_room_id', 'sender_id', 'receiver_id', 'message', 'is_read'];

    public function sender() {
    	return $this->hasOne(User::class, 'id', 'sender_id');
    }

    protected static function readChatMessages($senderId){
        return static::where('receiver_id', Auth::user()->id)->where('sender_id', $senderId)->where('is_read', 0)->update(['is_read' => 1]);
    }

    protected static function showchatusers(){
    	$chatusers = [];
        $chatmessageusers = [];
        if(Cache::has('vchip:user-'.Auth()->user()->id.':chatusers')){
            $userResult['chatusers'] = Cache::get('vchip:user-'.Auth()->user()->id.':chatusers');
        } else {
            $result = static::where('sender_id', Auth()->user()->id)->Orwhere('receiver_id',  Auth()->user()->id)->take(10)->get();
            if(is_object($result) && false == $result->isEmpty()){
                foreach($result as $message){
                    if(Auth()->user()->id != $message->sender_id){
                        $chatmessageusers[] = $message->sender_id;
                    } else {
                        $chatmessageusers[] = $message->receiver_id;
                    }
                }
            }
            $skipUsers =  array_unique($chatmessageusers);

            $chatusers['chat_users'][] = array_values($skipUsers);
            $messageusers = User::whereIn('id', $skipUsers)->get();
            if(is_object($messageusers) && false == $messageusers->isEmpty()){
                foreach($messageusers as $user){
                    $chatusers['users'][] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo,
                        'chat_room_id' => $user->chatroomid(),
                        'is_online' => $user->isOnline(),
                    ];
                }
            }
            array_push($skipUsers, Auth()->user()->id);
            $users = User::whereNotIn('id', $skipUsers)->skip(0)->take(10)->get();

        	if(is_object($users) && false == $users->isEmpty()){
        		foreach($users as $user){
        			$chatusers['users'][] = [
        				'id' => $user->id,
        				'name' => $user->name,
        				'photo' => $user->photo,
        				'chat_room_id' => $user->chatroomid(),
        				'is_online' => $user->isOnline(),
        			];
        		}
        	}
            Cache::put('vchip:user-'.Auth()->user()->id.':chatusers', $chatusers, 60);
    	    $userResult['chatusers'] = $chatusers;
        }
        $messageResult = static::where('receiver_id',  Auth()->user()->id)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        if(is_object($messageResult) && false == $messageResult->isEmpty()){
            foreach($messageResult as $message){
                $userResult['unreadCount'][$message->sender_id] = $message->unread;
            }
        }
        return $userResult;
    }

    protected function privatechat(Request $request){
		$receiverId = $request->get('receiver_id');

		$receiver = User::find($receiverId);
        $senderUserId = Auth::user()->id;
        $roomMembers = [$receiverId, $senderUserId];
        sort($roomMembers);
        $roomName = 'private_'.$roomMembers[0].'_'.$roomMembers[1];
        $roomMembers = implode($roomMembers, ',');
        DB::beginTransaction();
        try
	    {
	        $chatRoom = ChatRoom::where('user_ids', $roomMembers)->first();
	        if(is_null($chatRoom)) {
	            $chatRoom = new ChatRoom;
	            $chatRoom->room_type = 'private';
	            $chatRoom->user_ids = $roomMembers;
	            $chatRoom->save();
	            DB::commit();
	        }
	    }
        catch(\Exception $e)
        {
            DB::rollback();
            return;
        }
        $result['messages'] = static::where('chat_room_id', $chatRoom->id)->skip($request->get('message_limit'))->take(10)->orderBy('id', 'desc')->get();
        $result['chatroom_id'] = $chatRoom->id;
        return $result;
	}

	protected static function sendMessage(Request $request){
		DB::beginTransaction();
        try
	    {
			$message = new static;
			$message->chat_room_id = $request->get('chatroomId');
			$message->sender_id = $request->get('sender');
			$message->receiver_id = $request->get('receiver');
			$message->message = $request->get('message');
	        $message->is_read = 0;
			$message->save();
			DB::commit();
			return $message;
		}
        catch(\Exception $e)
        {
            DB::rollback();
            return;
        }
	}
}