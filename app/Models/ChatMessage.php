<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\User;
use App\Models\ChatRoom;
use DB,Auth,Cache,LRedis;

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
        $initialChatuserIds = [];
        $chatmessageusers = [];
        $loginUser = Auth()->user();
        if(Cache::has('vchip:user-'.$loginUser->id.':chatusers')){
            $userResult['chatusers'] = Cache::get('vchip:user-'.$loginUser->id.':chatusers');
        } else {

            $adminChatUser = User::where('email', 'ceo@vchiptech.com')->first();
            if(is_object($adminChatUser)){
                $adminChatUserId = $adminChatUser->id;
            } else {
                $adminChatUserId = 0;
            }

            $result = static::where('sender_id', $loginUser->id)->Orwhere('receiver_id',  $loginUser->id)->orderBy('id', 'desc')->get();

            if(is_object($result) && false == $result->isEmpty()){
                foreach($result as $message){
                    if($loginUser->id != $message->sender_id && $adminChatUserId != $message->sender_id){
                        if(!in_array($message->sender_id, $chatmessageusers)){
                            $chatmessageusers[] = $message->sender_id;
                        }
                    } else {
                        if($adminChatUserId != $message->receiver_id){
                            if(!in_array($message->receiver_id, $chatmessageusers) && $message->receiver_id != $loginUser->id){
                                $chatmessageusers[] = $message->receiver_id;
                            }
                        }
                    }
                }
            }

            $orderById = implode(',', $chatmessageusers);
            if(count($chatmessageusers) > 0){
                if('ceo@vchiptech.com' == $loginUser->email){
                    $messageusers = User::whereIn('id', $chatmessageusers)->where('verified',1)->where('admin_approve',1)->orderByRaw(DB::raw("FIELD(id,$orderById)"))->get();
                } else {
                    $messageusers = User::where('college_id',$loginUser->college_id)->whereIn('id', $chatmessageusers)->where('verified',1)->where('admin_approve',1)->orderByRaw(DB::raw("FIELD(id,$orderById)"))->get();
                }
                if(is_object($messageusers) && false == $messageusers->isEmpty()){
                    foreach($messageusers as $user){
                        if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                            $isImageExist = 'system';
                        } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                            $isImageExist = 'other';
                        } else {
                            $isImageExist = 'false';
                        }
                        if(User::Student == $user->user_type){
                            $userType = 'Student';
                        } elseif(User::Lecturer == $user->user_type){
                            $userType = 'Lecturer';
                        } elseif(User::Hod == $user->user_type){
                            $userType = 'Hod';
                        } elseif(User::Directore == $user->user_type){
                            $userType = 'Director';
                        } elseif(User::TNP == $user->user_type){
                            $userType = 'TNP';
                        }
                        $initialChatuserIds[] = $user->id;
                        $chatusers['users'][] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'photo' => $user->photo,
                            'image_exist' => $isImageExist,
                            'chat_room_id' => $user->chatroomid(),
                            'college' => $userType,
                        ];
                    }
                }
            }

            array_push($chatmessageusers, $adminChatUserId);
            array_push($chatmessageusers, $loginUser->id);
            if('ceo@vchiptech.com' == $loginUser->email){
                $users = User::whereNotIn('id', $chatmessageusers)->where('verified',1)->where('admin_approve',1)->orderBy('name','asc')->take(10)->get();
            } else {
                $users = User::where('college_id',$loginUser->college_id)->whereNotIn('id', $chatmessageusers)->where('verified',1)->where('admin_approve',1)->orderBy('name','asc')->take(10)->get();
            }
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                    if(User::Student == $user->user_type){
                        $userType = 'Student';
                    } elseif(User::Lecturer == $user->user_type){
                        $userType = 'Lecturer';
                    } elseif(User::Hod == $user->user_type){
                        $userType = 'Hod';
                    } elseif(User::Directore == $user->user_type){
                        $userType = 'Director';
                    } elseif(User::TNP == $user->user_type){
                        $userType = 'TNP';
                    }
                    $initialChatuserIds[] = $user->id;
                    $chatusers['users'][] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo,
                        'image_exist' => $isImageExist,
                        'chat_room_id' => $user->chatroomid(),
                        'college' => $userType,
                    ];
                }
            }
            $newChatArray = array_merge($initialChatuserIds, $chatmessageusers);
            $chatusers['chat_users'] = implode(',', array_unique($newChatArray));
            Cache::put('vchip:user-'.$loginUser->id.':chatusers', $chatusers, 60);
            $userResult['chatusers'] = $chatusers;
        }
        $messageResult = static::where('receiver_id',  $loginUser->id)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        if(is_object($messageResult) && false == $messageResult->isEmpty()){
            foreach($messageResult as $message){
                $userResult['unreadCount'][$message->sender_id] = $message->unread;
            }
        } else {
            $userResult['unreadCount'] = [];
        }

        $userResult['onlineUsers'] = static::checkOnlineUsers();
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

    protected static function checkOnlineUsers(){
        $onlineUsers = LRedis::scan(0, 'match', "vchip:online_user-*")[1];
        $onlineUserIds = [];
        if(count($onlineUsers) > 0){
            foreach($onlineUsers as $onlineUser){
                $userId = (int) explode('-', $onlineUser)[1];
                $onlineUserIds[$userId] = $userId;
            }
        }
        return $onlineUserIds;
    }

    protected static function deleteChatMessagesByUserId($userId){
        $chatRoomIds = [];
        $result = static::where('sender_id', $userId)->Orwhere('receiver_id',  $userId)->get();
        if(is_object($result) && false == $result->isEmpty()){
            foreach($result as $message){
                $chatRoomIds[] = $message->chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        if(count($chatRoomIds) > 0){
            $chatRooms = ChatRoom::findMany($chatRoomIds);
            if(is_object($chatRooms) && false == $chatRooms->isEmpty()){
                foreach($chatRooms as $chatRoom){
                    $chatRoom->delete();
                }
            }
        }
        return;
    }
}