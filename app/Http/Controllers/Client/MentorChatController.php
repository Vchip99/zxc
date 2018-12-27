<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use LRedis, Auth, Cache, DB;
use App\Models\User;
use App\Models\Mentor;
use App\Models\MentorChatRoom;
use App\Models\MentorChatMessage;

class MentorChatController extends Controller
{
    public function __construct()
	{

	}

	public function userMentorSendMessage($subdomainName,Request $request){
        return MentorChatMessage::sendMessage($request);
	}

	protected function privatechatByUser($subdomainName,Request $request){
        MentorChatMessage::privatechatByUser($request);
        return redirect()->back()->with('message', 'Message send successfully.');
	}

    protected function userMentorPrivateChat($subdomainName,Request $request){
        return MentorChatMessage::userMentorPrivateChat($request);
    }
}