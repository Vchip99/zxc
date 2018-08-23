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
        return ChatMessage::sendMessage($request);
	}

	protected function privatechat(Request $request){
        return ChatMessage::privatechat($request);
	}

	protected function showchatusers(){
        return ChatMessage::showchatusers();
    }

    protected function loadChatUsers(Request $request){
        $chatusers = [];
        $skipUsers = [];
        $limitStart = $request->get('limit_start');
        $loginUser = Auth()->user();
        if(Cache::has('vchip:user-'.$loginUser->id.':chatusers:limitStart-'.$limitStart)){
            $result['chatusers'] = Cache::get('vchip:user-'.$loginUser->id.':chatusers:limitStart-'.$limitStart);
        } else {
            $skipUsers = explode(',', $request->get('previuos_chat_users'));
            array_push($skipUsers, $loginUser->id);
            $users = User::whereNotIn('id', $skipUsers)->take(10)->get();

            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                    $chatusers[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo,
                        'image_exist' => $isImageExist,
                        'chat_room_id' => $user->chatroomid(),
                        'college' => $user->getCollegeName(),
                    ];
                }
            }
            Cache::put('vchip:user-'.$loginUser->id.':chatusers:limitStart-'.$limitStart, $chatusers, 60);
            $result['chatusers'] = $chatusers;
        }
        $result['unreadCount'] = ChatMessage::where('receiver_id',  $loginUser->id)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        $result['onlineUsers'] = ChatMessage::checkOnlineUsers();
        return $result;
    }

    protected function checkOnlineUsers(){
        return ChatMessage::checkOnlineUsers();
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