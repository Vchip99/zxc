<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientChatRoom;
use DB,Auth,Cache,LRedis;

class ClientChatMessage extends Model
{
	protected $connection = 'mysql2';

    protected $fillable = ['client_chat_room_id', 'sender_id', 'receiver_id', 'message', 'is_read', 'client_id', 'created_by_client'];

    protected function privatechat(Request $request){
		$receiverId = $request->get('receiver_id');
		if(is_object(Auth::guard('clientuser')->user())){
			$loginUser = Auth::guard('clientuser')->user();
	        $senderUserId = $loginUser->id;
	        $clientUserId = $loginUser->id;
	        $clientId = $loginUser->client_id;
	        $roomMembers = [$clientId, $senderUserId];
	        $createdByClient = 1;
		} else {
			$loginUser = Auth::guard('client')->user();
	        $senderUserId = $loginUser->id;
	        $clientId = $loginUser->id;
	        $roomMembers = [$clientId, $receiverId];
	        $clientUserId = $loginUser->id;
	        $createdByClient = 0;
		}
        $roomName = 'private_'.$roomMembers[0].'_'.$roomMembers[1];
        $roomMembers = implode($roomMembers, ',');
        DB::connection('mysql2')->beginTransaction();
        try
	    {
	        $chatRoom = ClientChatRoom::where('user_ids', $roomMembers)->where('client_id', $clientId)->first();

	        if(is_null($chatRoom)) {
	            $chatRoom = new ClientChatRoom;
	            $chatRoom->room_type = 'private';
	            $chatRoom->user_ids = $roomMembers;
	            $chatRoom->client_id = $clientId;
	            $chatRoom->save();
	            DB::connection('mysql2')->commit();
	        }
	    }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return;
        }
        $result['messages'] = static::where('client_chat_room_id', $chatRoom->id)->where('client_id', $clientId)->skip($request->get('message_limit'))->take(10)->orderBy('id', 'desc')->get();
        $result['chatroom_id'] = $chatRoom->id;
    	$result['unreadCount'] = static::where('receiver_id',  $clientUserId)->where('client_id', $clientId)->where('is_read', 0)->where('created_by_client', $createdByClient)->select( \DB::raw('count(*) as unread'))->get();
        return $result;
	}

	protected static function readClientChatMessages($senderId){
		if(is_object(Auth::guard('clientuser')->user())){
			$loginUser = Auth::guard('clientuser')->user();
	        $receiverId = $loginUser->id;
	        $clientId = $loginUser->client_id;
	        $createdByClient = 1;
		} else {
			$loginUser = Auth::guard('client')->user();
	        $receiverId = $loginUser->id;
	        $clientId = $loginUser->id;
	        $createdByClient = 0;
		}
        return static::where('receiver_id', $receiverId)->where('sender_id', $senderId)->where('client_id', $clientId)->where('is_read', 0)->where('created_by_client', $createdByClient)->update(['is_read' => 1]);
    }

    protected static function sendMessage(Request $request){
		DB::connection('mysql2')->beginTransaction();
        try
	    {
			$message = new static;
			$message->client_chat_room_id = $request->get('chatroomId');
			$message->sender_id = $request->get('sender');
			$message->receiver_id = $request->get('receiver');
			$message->message = $request->get('message');
	        $message->is_read = 0;
	        $message->client_id = $request->get('client_id');
	        if(1 == $request->get('created_by_client')){
	        	$message->created_by_client = 1;
	        } else {
	        	$message->created_by_client = 0;
	        }
			$message->save();
			DB::connection('mysql2')->commit();
			return $message;
		}
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return;
        }
	}

	protected static function showClientChatUsers($subdomainName){
        $chatusers = [];
        $chatmessageusers = [];
        $loginUser = Auth::guard('client')->user();
		$clientId = $loginUser->id;

        $result = static::where('sender_id', $clientId)->Orwhere('receiver_id',  $clientId)->orderBy('id', 'desc')->get();

        if(is_object($result) && false == $result->isEmpty()){
            foreach($result as $message){
                if($clientId != $message->sender_id && !in_array($message->sender_id, $chatmessageusers)){
                    $chatmessageusers[] = $message->sender_id;
                } else if($clientId != $message->receiver_id && !in_array($message->receiver_id, $chatmessageusers)){
                    $chatmessageusers[] = $message->receiver_id;
                }
            }
        }

        $chatusers['chat_users'] = $orderById = implode(',', $chatmessageusers);
        if(count($chatmessageusers) > 0){
            if( 1 == $loginUser->allow_non_verified_email){
                $messageusers = Clientuser::whereIn('id', $chatmessageusers)->where('client_id', $clientId)->where('client_approve',1)->whereNotNull('email')->orderByRaw(DB::raw("FIELD(id,$orderById)"))->get();
            } else {
                $messageusers = Clientuser::whereIn('id', $chatmessageusers)->where('client_id', $clientId)->where('verified',1)->where('client_approve',1)->orderByRaw(DB::raw("FIELD(id,$orderById)"))->get();
            }
            if(is_object($messageusers) && false == $messageusers->isEmpty()){
                foreach($messageusers as $user){
                    if(is_file($user->photo) && true == preg_match('/clientUserStorage/',$user->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($user->photo) && false == preg_match('/clientUserStorage/',$user->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                    $chatusers['users'][] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo,
                        'image_exist' => $isImageExist,
                        'chat_room_id' => $user->chatroomid(),
                    ];
                }
            }
        }
        if( 1 == $loginUser->allow_non_verified_email){
            $users = Clientuser::whereNotIn('id', $chatmessageusers)->where('client_id', $clientId)->whereNotNull('email')->where('client_approve',1)->skip(0)->take(10)->get();
        } else {
            $users = Clientuser::whereNotIn('id', $chatmessageusers)->where('client_id', $clientId)->where('verified',1)->where('client_approve',1)->skip(0)->take(10)->get();
        }

        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if(is_file($user->photo) && true == preg_match('/clientUserStorage/',$user->photo)){
                    $isImageExist = 'system';
                } else if(!empty($user->photo) && false == preg_match('/clientUserStorage/',$user->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
                $chatusers['users'][] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'photo' => $user->photo,
                    'image_exist' => $isImageExist,
                    'chat_room_id' => $user->chatroomid(),
                ];
            }
        }

        $mobileUsers = Clientuser::whereNotIn('id', $chatmessageusers)->where('client_id', $clientId)->whereNotNull('phone')->where('number_verified',1)->where('client_approve',1)->get();

        if(is_object($mobileUsers) && false == $mobileUsers->isEmpty()){
            foreach($mobileUsers as $mobileUser){
                if(is_file($mobileUser->photo) && true == preg_match('/clientUserStorage/',$mobileUser->photo)){
                    $isImageExist = 'system';
                } else if(!empty($mobileUser->photo) && false == preg_match('/clientUserStorage/',$mobileUser->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
                $chatusers['users'][] = [
                    'id' => $mobileUser->id,
                    'name' => $mobileUser->name,
                    'photo' => $mobileUser->photo,
                    'image_exist' => $isImageExist,
                    'chat_room_id' => $mobileUser->chatroomid(),
                ];
            }
        }

        $userResult['chatusers'] = $chatusers;

        $messageResult = static::where('receiver_id',  $clientId)->where('client_id', $clientId)->where('is_read', 0)->where('created_by_client', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        if(is_object($messageResult) && false == $messageResult->isEmpty()){
            foreach($messageResult as $message){
                $userResult['unreadCount'][$message->sender_id] = $message->unread;
            }
        } else {
            $userResult['unreadCount'] = [];
        }

        $userResult['onlineUsers'] = static::checkOnlineUsers($subdomainName);
        return $userResult;
    }

    protected static function checkOnlineUsers($subdomainName){
        $onlineUsers = LRedis::scan(0, 'match', $subdomainName.":online_user-*")[1];
        $onlineUserIds = [];
        if(count($onlineUsers) > 0){
            foreach($onlineUsers as $onlineUser){
                $userId = (int) explode('-', $onlineUser)[1];
                $onlineUserIds[$userId] = $userId;
            }
        }
        return $onlineUserIds;
    }

    protected static function deleteClientChatMessagesByClientIdByUserId($clientId,$userId){
        $chatRoomIds = [];
        $senderResult = static::where('sender_id', $userId)->where('client_id', $clientId)->where('created_by_client', 0)->get();
        if(is_object($senderResult) && false == $senderResult->isEmpty()){
            foreach($senderResult as $message){
                $chatRoomIds[] = $message->client_chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        $receiverResult = static::where('receiver_id', $userId)->where('client_id', $clientId)->where('created_by_client', 1)->get();
        if(is_object($receiverResult) && false == $receiverResult->isEmpty()){
            foreach($receiverResult as $message){
                $chatRoomIds[] = $message->client_chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        if(count($chatRoomIds) > 0){
            $chatRooms = ClientChatRoom::findMany($chatRoomIds);
            if(is_object($chatRooms) && false == $chatRooms->isEmpty()){
                foreach($chatRooms as $chatRoom){
                    $chatRoom->delete();
                }
            }
        }
        return;
    }

    protected static function deleteClientChatMessagesByClientId($clientId){
        $result = static::where('client_id', $clientId)->get();
        if(is_object($result) && false == $result->isEmpty()){
            foreach($result as $message){
                $message->delete();
            }
        }
        $chatRooms = ClientChatRoom::where('client_id', $clientId)->get();
        if(is_object($chatRooms) && false == $chatRooms->isEmpty()){
            foreach($chatRooms as $chatRoom){
                $chatRoom->delete();
            }
        }
        return;
    }
}