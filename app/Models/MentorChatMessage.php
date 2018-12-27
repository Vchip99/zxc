<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\User;
use App\Models\Mentor;
use App\Models\MentorChatRoom;
use DB,Auth,Cache,LRedis;

class MentorChatMessage extends Model
{
    protected $fillable = ['mentor_chat_room_id', 'sender_id', 'receiver_id', 'message', 'is_read','generated_by_mentor'];

    protected function privatechatByUser(Request $request){
        $receiverId = $request->get('receiver_id');
        $senderUserId = $request->get('sender_id');
        if(is_object(Auth::guard('mentor')->user())){
            $roomMembers = [$receiverId, $senderUserId];
            $clientUserId = $loginUser->id;
            $createdByMentor = 1;
        } else {
            $roomMembers = [$receiverId, $senderUserId];
            $createdByMentor = 0;
        }
        sort($roomMembers);
        $roomName = 'private_'.$roomMembers[0].'_'.$roomMembers[1];
        $roomMembers = implode($roomMembers, ',');
        DB::beginTransaction();
        try
        {
            $chatRoom = MentorChatRoom::where('user_ids', $roomMembers)->first();
            if(is_null($chatRoom)) {
                $chatRoom = new MentorChatRoom;
                $chatRoom->room_type = 'private';
                $chatRoom->user_ids = $roomMembers;
                $chatRoom->save();
                DB::commit();
            }
            $message = new static;
            $message->mentor_chat_room_id = $chatRoom->id;
            $message->sender_id = $senderUserId;
            $message->receiver_id = $receiverId;
            $message->message = $request->get('message');
            $message->is_read = 0;
            if(is_object(Auth::guard('mentor')->user())){
                $message->generated_by_mentor = 1;
            } else {
                $message->generated_by_mentor = 0;
            }
            $message->save();
            DB::commit();
            // send message to mentor
            $mentor = Mentor::find($receiverId);
            if(is_object($mentor)){
                $mobile = $mentor->mobile;
                $msg = 'From:'.Auth::user()->name.'-'.$request->get('message');
                $message =mb_strimwidth($msg, 0, 150, "...");
                InputSanitise::sendSms($mobile,$message);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return;
        }
        return;
     //    $result['messages'] = static::where('mentor_chat_room_id', $chatRoom->id)->where('client_id', $clientId)->skip($request->get('message_limit'))->take(10)->orderBy('id', 'desc')->get();
     //    $result['chatroom_id'] = $chatRoom->id;
    	// $result['unreadCount'] = static::where('receiver_id',  $clientUserId)->where('client_id', $clientId)->where('is_read', 0)->where('generated_by_mentor', $createdByClient)->select( \DB::raw('count(*) as unread'))->get();
     //    return $result;
	}

    protected function userMentorPrivateChat(Request $request){
        $receiverId = $request->get('receiver_id');
        $senderUserId = $request->get('sender_id');

        $roomMembers = [$receiverId, $senderUserId];
        sort($roomMembers);
        $roomName = 'private_'.$roomMembers[0].'_'.$roomMembers[1];
        $roomMembers = implode($roomMembers, ',');
        DB::beginTransaction();
        try
        {
            $chatRoom = MentorChatRoom::where('user_ids', $roomMembers)->first();
            if(is_null($chatRoom)) {
                $chatRoom = new MentorChatRoom;
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
        $result['messages'] = static::where('mentor_chat_room_id', $chatRoom->id)->orderBy('id', 'desc')->get();
        $result['chatroom_id'] = $chatRoom->id;
        $result['unreadCount'] = static::where('receiver_id',  $receiverId)->where('is_read', 0)->where('generated_by_mentor', 1)->select( \DB::raw('count(*) as unread'))->get();
        return $result;
    }

    protected static function sendMessage(Request $request){
		DB::beginTransaction();
        try
	    {
			$message = new static;
			$message->mentor_chat_room_id = $request->get('chatroomId');
			$message->sender_id = $request->get('sender');
			$message->receiver_id = $request->get('receiver');
			$message->message = $request->get('message');
	        $message->is_read = 0;
	        if(1 == $request->get('generated_by_mentor')){
	        	$message->generated_by_mentor = 1;
	        } else {
	        	$message->generated_by_mentor = 0;
	        }
			$message->save();
			DB::commit();
			// return $message;
		}
        catch(\Exception $e)
        {
            DB::rollback();
            return;
        }
        $result['messages'] = static::where('mentor_chat_room_id', $request->get('chatroomId'))->orderBy('id', 'desc')->get();
        $result['chatroom_id'] = $request->get('chatroomId');
        $result['unreadCount'] = static::where('receiver_id',  $request->get('receiver'))->where('generated_by_mentor', $request->get('generated_by_mentor'))->select( \DB::raw('count(*) as unread'))->get();
        return $result;
	}

    protected static function deleteMentoChatMessagesByUserId($userId){
        $chatRoomIds = [];
        $senderResult = static::where('sender_id', $userId)->where('generated_by_mentor', 0)->get();
        if(is_object($senderResult) && false == $senderResult->isEmpty()){
            foreach($senderResult as $message){
                $chatRoomIds[] = $message->mentor_chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        $receiverResult = static::where('receiver_id', $userId)->where('generated_by_mentor', 1)->get();
        if(is_object($receiverResult) && false == $receiverResult->isEmpty()){
            foreach($receiverResult as $message){
                $chatRoomIds[] = $message->mentor_chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        if(count($chatRoomIds) > 0){
            $chatRooms = MentorChatRoom::findMany($chatRoomIds);
            if(is_object($chatRooms) && false == $chatRooms->isEmpty()){
                foreach($chatRooms as $chatRoom){
                    $chatRoom->delete();
                }
            }
        }
        return;
    }

    protected static function deleteMentorChatMessagesByMentorId($mentorId){
        $chatRoomIds = [];
        $senderResult = static::where('sender_id', $mentorId)->where('generated_by_mentor', 1)->get();
        if(is_object($senderResult) && false == $senderResult->isEmpty()){
            foreach($senderResult as $message){
                $chatRoomIds[] = $message->mentor_chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        $receiverResult = static::where('receiver_id', $mentorId)->where('generated_by_mentor', 0)->get();
        if(is_object($receiverResult) && false == $receiverResult->isEmpty()){
            foreach($receiverResult as $message){
                $chatRoomIds[] = $message->mentor_chat_room_id;
                $message->delete();
            }
            array_unique($chatRoomIds);
        }
        if(count($chatRoomIds) > 0){
            $chatRooms = MentorChatRoom::findMany($chatRoomIds);
            if(is_object($chatRooms) && false == $chatRooms->isEmpty()){
                foreach($chatRooms as $chatRoom){
                    $chatRoom->delete();
                }
            }
        }
        return;
    }
}
