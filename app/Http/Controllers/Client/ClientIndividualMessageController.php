<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator,Session,Auth,DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\ClientBatch;
use App\Models\Clientuser;
use App\Models\ClientIndividualMessage;

class ClientIndividualMessageController extends ClientBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateMessage = [
        'batch' => 'required',
    ];

    protected function show($subdomainName,Request $request){
        $loginUser = Auth::guard('client')->user();
        $date = date('Y-m-d');
        $messages = ClientIndividualMessage::getIndividualMessagesByClientIdByDate($loginUser->id,$date);
        return view('client.individualMessage.list', compact('messages','subdomainName','date'));
    }

    /**
     *  create message
     */
    protected function create($subdomainName,Request $request){
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        $messages = [];
        $batchUsers = [];
        $individualMessage = '';
        return view('client.individualMessage.create', compact('individualMessage','messages','batches', 'subdomainName','batchUsers'));
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
        $allUsersMessages = $request->except(['_token','batch']);
        $batchId   = InputSanitise::inputInt($request->get('batch'));
        $allMessagesString = '';
        $studentsData = [];
        if(count($allUsersMessages) > 0){
            foreach($allUsersMessages as $userId => $message){
                if(!empty($message)){
                    if(empty($allMessagesString)){
                        $allMessagesString = $userId.':'.$message;
                    } else {
                        $allMessagesString .= ','.$userId.':'.$message;
                    }
                    $studentsData[$userId] = $message;
                }
            }
        }
        if(empty($allMessagesString) && 0 == count($studentsData)){
            return redirect()->back()->withErrors('please enter message atleast one user');
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $message = ClientIndividualMessage::addClientIndividualMessage($allMessagesString,$batchId);
            if(is_object($message)){
                $this->sendIndividualMessages($batchId,$studentsData);
                DB::connection('mysql2')->commit();
                return Redirect::to('manageIndividualMessage')->with('message', 'Individual Message created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageIndividualMessage');
    }

    /**
     *  edit message
     */
    protected function edit($subdomainName,Request $request, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $individualMessage = ClientIndividualMessage::find($id);
            if(is_object($individualMessage)){
                $messages = [];
                $batches = ClientBatch::getBatchesByClientId($individualMessage->client_id);
                if(!empty($individualMessage->messages)){
                    $allMessages = explode(',', $individualMessage->messages);
                    if(count($allMessages) > 0){
                        foreach($allMessages as $userMessages){
                            $arrMsg = explode(':', $userMessages);
                            $messages[$arrMsg[0]] = $arrMsg[1];
                        }
                    }
                }
                $clientBatch = ClientBatch::getBatchById($individualMessage->client_batch_id);
                $batchUsers = [];
                if(is_object($clientBatch)){
                    $userIds = explode(',', $clientBatch->student_ids);
                    if(count($userIds) > 0){
                        $batchUsers = Clientuser::whereIn('id',$userIds)->where('client_approve', 1)->select('id','name','email')->get();
                    }
                }
                return view('client.individualMessage.create', compact('individualMessage','messages','batches', 'subdomainName','batchUsers'));
            }
        }
        return Redirect::to('manageMessage');
    }


    protected function delete(Request $request){
        $messageId = InputSanitise::inputInt($request->get('message_id'));
        $message = ClientIndividualMessage::find($messageId);
        if(is_object($message)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $message->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageIndividualMessage')->with('message', 'Individual Message deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageIndividualMessage');
    }

    protected function getIndividualMessagesByDate(Request $request){
        $result = [];
        $loginUser = Auth::guard('client')->user();
        $date = $request->get('date');
        $messages = ClientIndividualMessage::getIndividualMessagesByClientIdByDate($loginUser->id,$date);
        if(is_object($messages) && false == $messages->isEmpty()){
            foreach($messages as $message){
                $result[] = [
                    'id' => $message->id,
                    'batch' => $message->batch->name,
                    'date' => date('Y-m-d h:i:s a', strtotime($message->created_at)),
                ];
            }
        }
        return $result;
    }

    protected function sendIndividualMessages($batchId,$studentsData){
        $client = Auth::guard('client')->user();
        $sendSmsStatus = $client->individual_sms;
        if(Client::None != $sendSmsStatus){
            if($batchId > 0){
                $clientBatch = ClientBatch::where('client_id',$client->id)->where('id',$batchId)->first();
                if(is_object($clientBatch)){
                    $batchName = $clientBatch->name;
                }
                InputSanitise::sendIndividualSms($studentsData,$sendSmsStatus,$batchName,$client);
            }
        }
        return;
    }
}