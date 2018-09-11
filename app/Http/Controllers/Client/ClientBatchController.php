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
use App\Models\ClientOfflinePayment;
use App\Models\ClientUploadTransaction;
use DateTime;

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
        // $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateBatch = [
        'name' => 'required',
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
        $batches = ClientBatch::where('client_id', $clientId)->paginate();
        return view('client.batch.list', compact('batches','subdomainName','loginUser'));
    }

    /**
     *  create batch
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
        $batch = new ClientBatch;
        return view('client.batch.create', compact('batch', 'subdomainName','loginUser'));
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
    protected function edit($subdomainName,Request $request,$id){
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
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $batch = ClientBatch::find($id);
            if(is_object($batch)){
                return view('client.batch.create', compact('batch', 'subdomainName','loginUser'));
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
                $loginUser = InputSanitise::getLoginUserByGuardForClient();
                if($batch->created_by > 0 && $loginUser->id != $batch->created_by){
                    return Redirect::to('manageBatch');
                }
                if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                    return Redirect::to('manageBatch');
                }
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
                ClientMessage::deleteMessagesByBatchIdsByClientId($batch->id,$batch->client_id);
                ClientOfflinePayment::deleteClientOfflinePaymentByBatchIdsByClientId($batch->id,$batch->client_id);
                ClientUploadTransaction::deleteClientUploadTransactionByBatchIdsByClientId($batch->id,$batch->client_id);
                // remove batch from users
                $allUsers = Clientuser::getAllStudentsByClientId($batch->client_id);
                if(is_object($allUsers) && false == $allUsers->isEmpty()){
                    foreach($allUsers as $user){
                        if($user->batch_ids){
                            $userBatchIds = explode(',', $user->batch_ids);
                            if(in_array($batch->id, $userBatchIds)){
                                $userBatchIds = array_diff($userBatchIds, [$batch->id]);
                                if(count($userBatchIds) > 0){
                                    sort($userBatchIds);
                                    $user->batch_ids = implode(',', $userBatchIds);
                                } else {
                                    $user->batch_ids = '';
                                }
                                $user->save();
                            }
                        }
                    }
                }
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

    protected function showBatchStudents($subdomainName, Request $request){
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
        $batches = ClientBatch::getBatchesByClientId($clientId);
        $students = Clientuser::getAllStudentsByClientId($clientId);
        return view('client.batch.batch_student', compact('batches', 'students', 'subdomainName','loginUser'));
    }

    protected function associateBatchStudents($subdomainName, Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $batchId   = InputSanitise::inputInt($request->get('batch'));
            $batchObj = ClientBatch::getBatchById($batchId);
            $batchOldStudents = [];
            if(is_object($batchObj)){
                if(!empty($batchObj->student_ids)){
                    $batchOldStudents = explode(',', $batchObj->student_ids);
                } else {
                    $batchOldStudents = [];
                }
            } else {
                return Redirect::to('associateBatchStudents')->withErrors('please select batch.');
            }
            $clientBatch = ClientBatch::associateBatchStudents($request);
            if(is_object($clientBatch)){
                $userIds = explode(',', $clientBatch->student_ids);
                if(count($userIds) > 0){
                    $batchUsers = Clientuser::whereIn('id',$userIds)->where('client_approve', 1)->get();
                    if(is_object($batchUsers) && false == $batchUsers->isEmpty()){
                        foreach($batchUsers as $batchUser){
                            if($batchUser->batch_ids){
                                $userBatchIds = explode(',', $batchUser->batch_ids);
                                if(!in_array($clientBatch->id, $userBatchIds)){
                                    if(empty(trim($batchUser->batch_ids))){
                                        $batchUser->batch_ids = $clientBatch->id;
                                    } else {
                                        $batchUser->batch_ids .= ','.$clientBatch->id;
                                    }
                                }
                            } else {
                                $batchUser->batch_ids = $clientBatch->id;
                            }
                            $batchUser->save();
                        }
                    }
                }
                // remove batch id from un associated user and delete offline marks
                if(count(array_diff($batchOldStudents, $userIds)) > 0){
                    $studentIds = array_values(array_diff($batchOldStudents, $userIds));
                    ClientOfflinePaperMark::deleteMarksByClientIdByBatchIdByClientUsers($clientBatch->client_id,$clientBatch->id,$studentIds);
                    $oldBatchUsers = Clientuser::find($studentIds);
                    if(is_object($oldBatchUsers) && false == $oldBatchUsers->isEmpty()){
                        foreach($oldBatchUsers as $oldBatchUser){
                            if($oldBatchUser->batch_ids){
                                $userBatchIds = explode(',', $oldBatchUser->batch_ids);
                                if(in_array($clientBatch->id, $userBatchIds)){
                                    $userBatchIds = array_diff($userBatchIds, [$clientBatch->id]);
                                    if(count($userBatchIds) > 0){
                                        sort($userBatchIds);
                                        $oldBatchUser->batch_ids = implode(',', $userBatchIds);
                                    } else {
                                        $oldBatchUser->batch_ids = '';
                                    }
                                    $oldBatchUser->save();
                                }
                            }
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

    protected function showAttendance($subdomainName, Request $request){
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
        $attendanceDate = $request->get('attendance_date');
        $attendanceBatch = json_decode($request->get('batch_id'));
        $batches = ClientBatch::getBatchesByClientId($clientId);
        $batchUsers = [];
        $batchAttendance = [];
        $studentIds = '';
        if(!empty($attendanceDate) &&  !empty($attendanceBatch)){
            $clientBatch = ClientBatch::getBatchById($attendanceBatch);
            if(is_object($clientBatch)){
                $studentIds = $clientBatch->student_ids;
                $userIds = explode(',', $clientBatch->student_ids);
                if(count($userIds) > 0){
                    $batchUsers = Clientuser::whereIn('id',$userIds)->where('client_approve', 1)->get();
                }
            }
            $batchAttendanceObj = ClientUserAttendance::getBatchStudentAttendanceByBatchId($request);
            if(is_object($batchAttendanceObj)){
                $batchAttendance = explode(',', $batchAttendanceObj->student_ids);
            }
        }
        return view('client.attendance.attendance', compact('batches', 'subdomainName', 'attendanceDate', 'attendanceBatch', 'batchUsers', 'batchAttendance','studentIds','loginUser'));
    }

    protected function getBatchStudentAttendanceByBatchId(Request $request){
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
        $clientBatch = ClientBatch::getBatchById($batchId);
        $result = [];
        $batchUsers = [];
        if(is_object($clientBatch)){
            $userIds = explode(',', $clientBatch->student_ids);
            if(count($userIds) > 0){
                $batchUsers = Clientuser::whereIn('id',$userIds)->where('client_approve', 1)->get();
            }
        }
        $result['batchUsers'] = $batchUsers;
        $batchAttendance = ClientUserAttendance::getBatchStudentAttendanceByBatchId($request);
        $result['batchAttendance'] = [];
        if(is_object($batchAttendance)){
            $result['batchAttendance'] = explode(',', $batchAttendance->student_ids);
        }
        return $result;
    }

    protected function getBatchStudentsByBatchId(Request $request){
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
        $clientBatch = ClientBatch::getBatchById($batchId);
        $batchUsers = [];
        if(is_object($clientBatch)){
            $userIds = explode(',', $clientBatch->student_ids);
            if(count($userIds) > 0){
                $batchUsers = Clientuser::whereIn('id',$userIds)->where('client_approve', 1)->select('id','name','email')->get();
            }
        }
        return $batchUsers;
    }

    protected function markAttendance($subdomainName, Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $attendance = ClientUserAttendance::addOrUpdateClientUserAttendance($request);
            if(is_object($attendance)){
                if('client' == InputSanitise::getCurrentGuard()){
                    $client = Auth::guard('client')->user();
                    $sendSmsStatus = $client->absent_sms;
                } else {
                    $clientUser = Auth::guard('clientuser')->user();
                    $client = $clientUser->client;
                    $sendSmsStatus = $client->absent_sms;
                }
                if(Client::None != $sendSmsStatus){
                    $clientBatch = ClientBatch::where('client_id',$attendance->client_id)->where('id',$attendance->client_batch_id)->first();
                    if(is_object($clientBatch)){
                        if(!empty($clientBatch->student_ids)){
                            $allBatchStudents = explode(',', $clientBatch->student_ids);
                        } else {
                            $allBatchStudents = [];
                        }
                        if(!empty($attendance->student_ids)){
                            $presentStudents = explode(',', $attendance->student_ids);
                        } else {
                            $presentStudents = [];
                        }
                        $absentStudents = array_diff($allBatchStudents, $presentStudents);
                        if(count($absentStudents) > 0){
                            InputSanitise::sendAbsentSms($absentStudents,$sendSmsStatus,$clientBatch->name,$attendance->attendance_date,$client);
                        }
                    }
                }
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAttendanceCalendar')->with('message', 'Attendance mark successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAttendanceCalendar');
    }

    protected function showAttendanceCalendar($subdomainName, Request $request){
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
        $batches = ClientBatch::getBatchesByClientId($clientId);
        $selectedYear = json_decode($request->get('year'));
        $selectedBatch = json_decode($request->get('batch'));
        $batchesCount = [];
        $firstBatch = 0;
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $index => $batch){
                if(0 == $index){
                    $firstBatch = $batch->id;
                }
                $batchesCount[$batch->id] = count(explode(',',$batch->student_ids));
            }
        }
        if(!empty($selectedYear) && !empty($selectedBatch)){
            $result = $this->getAttendanceByBatchByYearByClient($selectedBatch,$selectedYear,$clientId,$batchesCount);
        } else {
            $selectedBatch = $firstBatch;
            $result = $this->getAttendanceByBatchByYearByClient($selectedBatch,date('Y'),$clientId,$batchesCount);

        }
        $attendanceStats = implode(',', $result['attendanceStats']);
        if(!empty($selectedYear)){
            $defaultDate = $selectedYear.'-'.date('m').'-'.date('d');
        } else {
            $defaultDate = date('Y-m-d');
        }
        $currnetYear = date('Y');
        $allAttendanceDates = implode(',', $result['allAttendanceDates']);
        return view('client.attendance.calendar', compact('batches', 'subdomainName', 'currnetYear','selectedYear','selectedBatch', 'allAttendanceDates','attendanceStats','loginUser','defaultDate'));
    }

    protected function getAttendanceByBatchByYearByClient($batch,$year,$clientId,$batchesCount){
        $attendanceStats = [];
        $result = [];
        $allAttendanceDates = [];
        if($batch > 0 && $year > 0){
            $allAttendance = ClientUserAttendance::where('client_batch_id','=', $batch)->whereYear('attendance_date', $year)->where('client_id', $clientId)->orderBy('attendance_date')->get();
        } else {
            $allAttendance = ClientUserAttendance::whereYear('attendance_date', $year)->where('client_id', $clientId)->orderBy('attendance_date')->get();
        }
        if(is_object($allAttendance) && false == $allAttendance->isEmpty()){
            foreach($allAttendance as $attendance){
                if(!empty($attendance->student_ids)){
                    $studentCount = count(explode(',',$attendance->student_ids));
                } else {
                    $studentCount = 0;
                }
                $presentCnt = $studentCount;
                if((int) $batchesCount[$attendance->client_batch_id] > (int) $studentCount){
                    $absentCnt = (int) $batchesCount[$attendance->client_batch_id] - (int) $studentCount;
                } else {
                    $absentCnt = (int) $studentCount - (int) $batchesCount[$attendance->client_batch_id];
                }
                $attendanceStats[] = $attendance->attendance_date.':'.$presentCnt.'-'.$absentCnt;
                $allAttendanceDates[] = $attendance->attendance_date;
            }
        }
        $result['allAttendanceDates'] = $allAttendanceDates;
        $result['attendanceStats'] = $attendanceStats;
        return $result;
    }

    protected function getBatchUsersByBatchId(Request $request){
        $batchId   = InputSanitise::inputInt(json_decode($request->get('batch_id')));
        $clientBatch = ClientBatch::getBatchById($batchId);
        $batchUsers = [];
        if(is_object($clientBatch)){
            $userIds = explode(',', $clientBatch->student_ids);
            if(count($userIds) > 0){
                $batchUsers = Clientuser::whereIn('id',$userIds)->where('client_approve', 1)->get();
            }
        }
        return $batchUsers;
    }
}