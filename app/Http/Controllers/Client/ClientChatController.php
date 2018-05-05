<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use LRedis, Auth, Cache, DB;
use App\Models\Clientuser;
use App\Models\ClientChatRoom;
use App\Models\ClientChatMessage;

class ClientChatController extends Controller
{
    public function __construct()
	{

	}

	public function sendMessage($subdomainName,Request $request){
        return ClientChatMessage::sendMessage($request);
	}

	protected function clientPrivateChat($subdomainName,Request $request){
        return ClientChatMessage::privatechat($request);
	}

	protected function showClientChatUsers($subdomainName){
        return ClientChatMessage::showClientChatUsers($subdomainName);
    }

    protected function loadClientChatUsers($subdomainName,Request $request){
        $chatusers = [];
        $skipUsers = [];
        $limitStart = $request->get('limit_start');
        $loginUser = Auth::guard('client')->user();
        $clientId = $loginUser->id;
        $skipUsers = explode(',', $request->get('previuos_chat_users'));
        $users = Clientuser::whereNotIn('id', $skipUsers)->where('client_id', $clientId)->skip($limitStart)->take(10)->get();

        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if(is_file($user->photo) && true == preg_match('/clientUserStorage/',$user->photo)){
                    $isImageExist = 'system';
                } else if(!empty($user->photo) && false == preg_match('/clientUserStorage/',$user->photo)){
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
                ];
            }
        }
        $result['chatusers'] = $chatusers;
        $result['unreadCount'] = ClientChatMessage::where('receiver_id',  $clientId)->where('client_id', $clientId)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
        $result['onlineUsers'] = ClientChatMessage::checkOnlineUsers($subdomainName);
        return $result;
    }

    protected function checkOnlineUsers($subdomainName){
        return ClientChatMessage::checkOnlineUsers($subdomainName);
    }

    protected function readClientChatMessages($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $senderId = $request->get('sender_id');
            ClientChatMessage::readClientChatMessages($senderId);
            DB::connection('mysql2')->commit();
            return;

        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return;
    }

}