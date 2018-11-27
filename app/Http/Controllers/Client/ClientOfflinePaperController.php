<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use App\Models\ClientOfflinePaperMark;
use App\Models\ClientExam;

class ClientOfflinePaperController extends ClientBaseController
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

    protected function getClientExamsByBatchId(Request $request){
        $clientBatchId = InputSanitise::inputInt($request->get('batch_id'));
        return ClientExam::getClientExamsByBatchId($clientBatchId);
    }

    protected function manageExamMarks($subdomainName,Request $request){
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
        $papers = [];
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.offlinePaper.offlinePaperMarks', compact('papers', 'subdomainName', 'batches','loginUser'));
    }

    protected function getBatchStudentsAndMarksByBatchIdByExamId(Request $request){
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
        $clientBatch = ClientBatch::getBatchById($batchId);
        $result = [];
        $batchUsers = [];
        if(is_object($clientBatch)){
            $userIds = explode(',', $clientBatch->student_ids);
            if(count($userIds) > 0){
                $batchUsers = Clientuser::whereIn('id',$userIds)->select('id','name')->get();
            }
        }
        $result['batchUsers'] = $batchUsers;
        $paperMarks = ClientOfflinePaperMark::getOfflinePaperMarksByBatchIdByExamId($request);
        $result['studentMarks'] = [];
        if(is_object($paperMarks) && false == $paperMarks->isEmpty()){
            foreach($paperMarks as $paperMark){
                $result['studentMarks'][$paperMark->clientuser_id] = $paperMark;
            }
        }
        return $result;
    }

    protected function assignOfflinePaperMarks(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $result = ClientOfflinePaperMark::assignOfflinePaperMarks($request);
            if('true' == $result){
                if('client' == InputSanitise::getCurrentGuard()){
                    $client = Auth::guard('client')->user();
                    $sendSmsStatus = $client->offline_exam_sms;
                } else {
                    $clientUser = Auth::guard('clientuser')->user();
                    $client = $clientUser->client;
                    $sendSmsStatus = $client->offline_exam_sms;
                }
                if(Client::None != $sendSmsStatus){
                    $presentStudentsMark = [];
                    $clientExamId   = InputSanitise::inputInt($request->get('client_exam'));
                    $clientBatchId = InputSanitise::inputInt($request->get('batch'));
                    $totalMarks   = InputSanitise::inputInt($request->get('total_marks'));
                    $studentMarks = $request->except('_token','client_exam','batch','total_marks');
                    if(count($studentMarks) > 0){
                        foreach($studentMarks as $studentId => $studentMark){
                            if(!empty($studentMark)){
                                $presentStudentsMark[$studentId] = $studentMark;
                            }
                        }
                    }
                    if(count($presentStudentsMark) > 0){
                        $clientBatch = ClientBatch::find($clientBatchId);
                        $clientExam = ClientExam::find($clientExamId);
                        if(is_object($clientBatch) && is_object($clientExam)){
                            InputSanitise::sendOfflinePaperMarkSms($presentStudentsMark,$sendSmsStatus,$clientBatch->id,$clientBatch->name,$clientExam->topic,$totalMarks,$client);
                        }
                    }
                }
                DB::connection('mysql2')->commit();
                return Redirect::to('manageExamMarks')->with('message', 'Assign marks to student successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageExamMarks');
    }
}