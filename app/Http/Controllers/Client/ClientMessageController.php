<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator,Session,Auth,DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\ClientBatch;
use App\Models\Clientuser;
use App\Models\ClientMessage;

class ClientMessageController extends ClientBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        // $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateMessage = [
        'message' => 'required',
        'batch' => 'required',
    ];

    protected function show($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $messages = ClientMessage::where('client_id', $clientId)->orderBy('id', 'desc')->paginate();
        return view('client.message.list', compact('messages','subdomainName','loginUser'));
    }

    /**
     *  create message
     */
    protected function create($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $message = new ClientMessage;
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.message.create', compact('message','batches', 'subdomainName','loginUser'));
    }

    /**
     *  store message
     */
    protected function store($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateMessage);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $message = ClientMessage::addOrUpdateClientMessage($subdomainName,$request);
            if(is_object($message)){
                if($message->client_batch_id > 0){
                    $studentIds = [];
                    $batch = ClientBatch::find($message->client_batch_id);
                    if(is_object($batch)){
                        $studentIds = explode(',', $batch->student_ids);
                    }
                    if(count($studentIds) > 0){
                        $users = Clientuser::getStudentsByIds($studentIds);
                        $this->setMessageCount($users);
                    }
                } else {
                    $users = Clientuser::getAllStudentsByClientId($message->client_id);
                    $this->setMessageCount($users);
                }
                DB::connection('mysql2')->commit();
                return Redirect::to('manageMessage')->with('message', 'Message created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageMessage');
    }

    /**
     *  edit message
     */
    protected function edit($subdomainName,Request $request, $id){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $message = ClientMessage::find($id);
            if(is_object($message)){
                $batches = ClientBatch::getBatchesByClientId($message->client_id);
                return view('client.message.create', compact('message','batches', 'subdomainName','loginUser'));
            }
        }
        return Redirect::to('manageMessage');
    }

    /**
     *  update message
     */
    protected function update($subdomainName,Request $request){
        $messageId = InputSanitise::inputInt($request->get('message_id'));
        if(isset($messageId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $message = ClientMessage::addOrUpdateClientMessage($subdomainName,$request, true);
                if(is_object($message)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageMessage')->with('message', 'Message updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageMessage');
    }

    protected function delete(Request $request){
        $messageId = InputSanitise::inputInt($request->get('message_id'));
        $message = ClientMessage::find($messageId);
        if(is_object($message)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $loginUser = InputSanitise::getLoginUserByGuardForClient();
                if($message->created_by > 0 && $loginUser->id != $message->created_by){
                    return Redirect::to('manageMessage');
                }
                if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                    return Redirect::to('manageMessage');
                }
                $dir = dirname($message->photo);
                InputSanitise::delFolder($dir);
                $message->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageMessage')->with('message', 'Message deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageMessage');
    }

    protected function setMessageCount($users){
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if(0 == $user->unread_messages || empty($user->unread_messages)){
                    $user->unread_messages = 1;
                } else {
                    $user->unread_messages++;
                }
                $user->save();
            }
        }
        return;
    }
}