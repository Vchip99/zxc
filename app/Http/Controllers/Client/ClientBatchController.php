<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator,Session,Auth,DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\ClientBatch;
use App\Models\Clientuser;
use App\Models\ClientUserAttendance;
use App\Models\ClientOfflinePaper;
use App\Models\ClientOfflinePaperMark;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentAnswer;
use App\Models\ClientMessage;

class ClientBatchController extends ClientBaseController
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
    protected $validateBatch = [
        'name' => 'required',
    ];

    protected function show($subdomainName){
        $batches = ClientBatch::where('client_id', Auth::guard('client')->user()->id)->paginate();
        return view('client.batch.list', compact('batches','subdomainName'));
    }

    /**
     *  create batch
     */
    protected function create($subdomainName){
        $batch = new ClientBatch;
        return view('client.batch.create', compact('batch', 'subdomainName'));
    }

    /**
     *  store batch
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateBatch);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $batch = ClientBatch::addOrUpdateClientBatch($request);
            if(is_object($batch)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageBatch')->with('message', 'Batch created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageBatch');
    }

    /**
     *  edit batch
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $batch = ClientBatch::find($id);
            if(is_object($batch)){
                return view('client.batch.create', compact('batch', 'subdomainName'));
            }
        }
        return Redirect::to('manageBatch');
    }

    /**
     *  update batch
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateBatch);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        if(isset($batchId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $batch = ClientBatch::addOrUpdateClientBatch($request, true);
                if(is_object($batch)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageBatch')->with('message', 'Batch updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageBatch');
    }

    protected function delete(Request $request){
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        $batch = ClientBatch::find($batchId);
        if(is_object($batch)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                ClientUserAttendance::deleteAttendanceByBtachIdByClientId($batch->id,$batch->client_id);
                ClientOfflinePaper::deleteOfflinePaperseByBtachIdByClientId($batch->id,$batch->client_id);
                ClientOfflinePaperMark::deleteClientOfflinePaperMarkByBatchIdByClientId($batch->id,$batch->client_id);
                ClientAssignmentSubject::deleteAssignmentSubjectsByBatchIdByClientId($batch->id,$batch->client_id);
                ClientAssignmentTopic::deleteAssignmentTopicsByBatchIdByClientId($batch->id,$batch->client_id);
                $assignments = ClientAssignmentQuestion::getClientAssignmentQuestionsByBatchIdByClientId($batch->id,$batch->client_id);
                if(is_object($assignments) && false == $assignments->isEmpty()){
                    foreach($assignments as $assignment){
                        $answers = ClientAssignmentAnswer::getClientAssignmentAnswersByAssignmentIdByClientId($assignment->id,$assignment->client_id);
                        if(is_object($answers) && false == $answers->isEmpty()){
                            foreach($answers as $answer){
                                $dir = dirname($answer->attached_link);
                                InputSanitise::delFolder($dir);
                                $answer->delete();
                            }
                        }
                        $dir = dirname($assignment->attached_link);
                        InputSanitise::delFolder($dir);
                        $assignment->delete();
                    }
                }
                ClientMessage::deleteMessagesByBatchIdsByClientId($batch->id,$batch->client_id)
                $batch->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageBatch')->with('message', 'Batch deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageBatch');
    }

    protected function showBatchStudents($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        $students = Clientuser::getAllStudentsByClientId($loginUser->id);
        return view('client.batch.batch_student', compact('batches', 'students', 'subdomainName'));
    }

    protected function associateBatchStudents($subdomainName, Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $clientBatch = ClientBatch::associateBatchStudents($request);
            if(is_object($clientBatch)){
                $userIds = explode(',', $clientBatch->student_ids);
                if(count($userIds) > 0){
                    $batchUsers = Clientuser::find($userIds);
                    if(is_object($batchUsers) && false == $batchUsers->isEMpty()){
                        foreach($batchUsers as $batchUser){
                            if($batchUser->batch_ids){
                                $userBatchIds = explode(',', $batchUser->batch_ids);
                                if(!in_array($clientBatch->id, $userBatchIds)){
                                    $batchUser->batch_ids .= ','.$clientBatch->id;
                                }
                            } else {
                                $batchUser->batch_ids = $clientBatch->id;
                            }
                            $batchUser->save();
                        }
                    }
                }
                DB::connection('mysql2')->commit();
                return Redirect::to('associateBatchStudents')->with('message', 'Student associate to batch successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('associateBatchStudents');
    }

    protected function getBatchStudentsIdsbyBatchId($subdomainName, Request $request){
        $batchId = $request->get('batch_id');
        return ClientBatch::getBatchById($batchId);
    }

    protected function searchClientStudent(Request $request){
        return Clientuser::searchClientStudent($request);
    }

    protected function showAttendance($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.attendance.attendance', compact('batches', 'subdomainName'));
    }

    protected function getBatchStudentAttendancebyBatchId(Request $request){
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
        $clientBatch = ClientBatch::getBatchById($batchId);
        $result = [];
        $batchUsers = [];
        if(is_object($clientBatch)){
            $userIds = explode(',', $clientBatch->student_ids);
            if(count($userIds) > 0){
                $batchUsers = Clientuser::find($userIds);
            }
        }
        $result['batchUsers'] = $batchUsers;
        $batchAttendance = ClientUserAttendance::getBatchStudentAttendancebyBatchId($request);
        $result['batchAttendance'] = [];
        if(is_object($batchAttendance)){
            $result['batchAttendance'] = explode(',', $batchAttendance->student_ids);
        }
        return $result;
    }

    protected function markAttendance($subdomainName, Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientUserAttendance::addOrUpdateClientUserAttendance($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('manageAttendance')->with('message', 'Attendance mark successfully!');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAttendance');
    }
}