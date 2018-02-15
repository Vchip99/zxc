<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use LRedis, Auth, Cache, DB;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;

class chatController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth');
	}
	public function sendMessage(Request $request){
		$message = new ChatMessage;
		$message->chat_room_id = $request->get('chatroomId');
		$message->sender_id = $request->get('sender');
		$message->receiver_id = $request->get('receiver');
		$message->message = $request->get('message');
        $message->is_read = 0;
		$message->save();
		return $message;
	}

	protected function privatechat(Request $request){
		$receiverId = $request->get('receiver_id');

		$receiver = User::find($receiverId);
        $senderUserId = Auth::user()->id;
        $roomMembers = [$receiverId, $senderUserId];
        sort($roomMembers);
        $roomName = 'private_'.$roomMembers[0].'_'.$roomMembers[1];
        $roomMembers = implode($roomMembers, ',');

        $chatRoom = ChatRoom::where('user_ids', $roomMembers)->first();
        if(is_null($chatRoom)) {
            $chatRoom = new ChatRoom;
            $chatRoom->room_type = 'private';
            $chatRoom->user_ids = $roomMembers;
            $chatRoom->save();
        }

        $result['messages'] = ChatMessage::where('chat_room_id', $chatRoom->id)->skip($request->get('message_limit'))->take(10)->orderBy('id', 'desc')->get();
        $result['chatroom_id'] = $chatRoom->id;
        return $result;
	}

	protected function showchatusers()
    {
    	$chatusers = [];
        $chatmessageusers = [];
        if(Cache::has('vchip:user-'.Auth()->user()->id.':chatusers')){
            $userResult['chatusers'] = Cache::get('vchip:user-'.Auth()->user()->id.':chatusers');
        } else {
            $result = ChatMessage::where('sender_id', Auth()->user()->id)->Orwhere('receiver_id',  Auth()->user()->id)->take(10)->get();
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
        $messageResult = ChatMessage::where('receiver_id',  Auth()->user()->id)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        if(is_object($messageResult) && false == $messageResult->isEmpty()){
            foreach($messageResult as $message){
                $userResult['unreadCount'][$message->sender_id] = $message->unread;
            }
        }
        return $userResult;
    }

    protected function loadChatUsers(Request $request){
        $chatusers = [];
        $skipUsers = [];
        $limitStart = $request->get('limit_start');
        if(Cache::has('vchip:user-'.Auth()->user()->id.':chatusers:limitStart-'.$limitStart)){
            $result['chatusers'] = Cache::get('vchip:user-'.Auth()->user()->id.':chatusers:limitStart-'.$limitStart);
        } else {
            array_push($skipUsers,$request->get('previuos_chat_users'));
            array_push($skipUsers, Auth()->user()->id);
            $users = User::whereNotIn('id', $skipUsers)->skip($limitStart)->take(10)->get();

            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $chatusers[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo,
                        'chat_room_id' => $user->chatroomid(),
                        'is_online' => $user->isOnline(),
                    ];
                }
            }
            Cache::put('vchip:user-'.Auth()->user()->id.':chatusers:limitStart-'.$limitStart, $chatusers, 60);
            $result['chatusers'] = $chatusers;
        }
        $result['unreadCount'] = ChatMessage::where('receiver_id',  Auth()->user()->id)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        return $result;
    }

    protected function checkOnlineUsers(){
        $onlineUsers = LRedis::scan(0, 'match', "vchip:online_user-*")[1];
        $onlineUserIds = [];
        if(count($onlineUsers) > 0){
            foreach($onlineUsers as $onlineUser){
                $onlineUserIds[] = (int) explode('-', $onlineUser)[1];
            }
        }
        return $onlineUserIds;
    }

    protected function readChatMessages(Request $request){
        DB::beginTransaction();
        try
        {
            $senderId = $request->get('sender_id');
            ChatMessage::readChatMessages($senderId);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return;
    }

}