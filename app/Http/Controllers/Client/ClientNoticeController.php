<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use App\Models\ClientNotice;

class ClientNoticeController extends ClientBaseController
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
    protected $validateClientNotice = [
        'batch' => 'required',
        'date' => 'required',
        'notice' => 'required',
        'is_emergency' => 'required',
    ];

    protected function show($subdomainName){
        $notices = ClientNotice::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate(50);
        return view('client.notice.list', compact('notices','subdomainName'));
    }

    /**
     *  create notice
     */
    protected function create($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $notice = new ClientNotice;
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.notice.create', compact('notice', 'subdomainName', 'batches'));
    }

    /**
     *  store notice
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientNotice);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $notice = ClientNotice::addOrUpdateClientNotice($request);
            if(is_object($notice)){
                DB::connection('mysql2')->commit();
                $this->sendNotice($notice);
                return Redirect::to('manageNotices')->with('message', 'Notice created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create notice.');
        }
        return Redirect::to('manageNotices');
    }

    /**
     *  edit notice
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $notice = ClientNotice::find($id);
            if(is_object($notice)){
                $batches = ClientBatch::getBatchesByClientId($notice->client_id);
                return view('client.notice.create', compact('notice', 'subdomainName', 'batches'));
            }
        }
        return Redirect::to('manageNotices');
    }

    /**
     *  update notice
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateClientNotice);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $noticeId = InputSanitise::inputInt($request->get('notice_id'));
        if(isset($noticeId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $notice = ClientNotice::addOrUpdateClientNotice($request, true);
                if(is_object($notice)){
                    DB::connection('mysql2')->commit();
                    $this->sendNotice($notice);
                    return Redirect::to('manageNotices')->with('message', 'Notice updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while update notice.');
            }
        }
        return Redirect::to('manageNotices');
    }

    /**
     *  delete notice
     */
    protected function delete(Request $request){
        $noticeId = InputSanitise::inputInt($request->get('notice_id'));
        $notice = ClientNotice::find($noticeId);
        if(is_object($notice)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $notice->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageNotices')->with('message', 'Notice deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete notice.');
            }
        }
        return Redirect::to('manageNotices');
    }

    protected function sendNotice($notice){
        $client = Auth::guard('client')->user();
        if(1 == $notice->is_emergency){
            $sendSmsStatus = $client->notice_sms;
        } else {
            $sendSmsStatus = $client->emergency_notice_sms;
        }
        if(Client::None != $sendSmsStatus){
            $allBatchStudents = [];
            if($notice->client_batch_id > 0){
                $clientBatch = ClientBatch::where('client_id',$notice->client_id)->where('id',$notice->client_batch_id)->first();
                if(is_object($clientBatch)){
                    if(!empty($clientBatch->student_ids)){
                        $allBatchStudents = explode(',', $clientBatch->student_ids);
                    }
                }
                $batchName = $clientBatch->name;
            } else {
                $batchName = 'All';
            }
            InputSanitise::sendNoticeSms($allBatchStudents,$sendSmsStatus,$notice->client_batch_id,$batchName,$notice->notice,$notice->is_emergency,$client->name,$client->id);
        }
    }
}