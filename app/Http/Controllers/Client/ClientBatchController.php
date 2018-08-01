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
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
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
                ClientMessage::deleteMessagesByBatchIdsByClientId($batch->id,$batch->client_id);
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
                    $batchUsers = Clientuser::find($userIds);
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
        // dd($request->all());
        $attendanceDate = $request->get('attendance_date');
        $attendanceBatch = json_decode($request->get('batch_id'));
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        $batchUsers = [];
        $batchAttendance = [];
        $studentIds = '';
        if(!empty($attendanceDate) &&  !empty($attendanceBatch)){
            $clientBatch = ClientBatch::getBatchById($attendanceBatch);
            if(is_object($clientBatch)){
                $studentIds = $clientBatch->student_ids;
                $userIds = explode(',', $clientBatch->student_ids);
                if(count($userIds) > 0){
                    $batchUsers = Clientuser::find($userIds);
                }
            }
            $batchAttendanceObj = ClientUserAttendance::getBatchStudentAttendancebyBatchId($request);
            if(is_object($batchAttendanceObj)){
                $batchAttendance = explode(',', $batchAttendanceObj->student_ids);
            }
        }
        return view('client.attendance.attendance', compact('batches', 'subdomainName', 'attendanceDate', 'attendanceBatch', 'batchUsers', 'batchAttendance','studentIds'));
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

    protected function showAttendanceCalendar($subdomainName, Request $request){
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
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
            $result = $this->getAttendanceByBatchByYearByClient($selectedBatch,$selectedYear,$loginUser->id,$batchesCount);
        } else {
            $selectedBatch = $firstBatch;
            $result = $this->getAttendanceByBatchByYearByClient($selectedBatch,date('Y'),$loginUser->id,$batchesCount);

        }
        if(!empty($selectedYear)){
            $defaultDate = $selectedYear.'-'.date('m').'-'.date('d');
        } else {
            $defaultDate = date('Y-m-d');
        }
        $currnetYear = date('Y');
        $events = $result['events'];
        $allAttendanceDates = implode(',', $result['allAttendanceDates']);
        $calendar = \Calendar::addEvents($events)->setOptions([ //set fullcalendar options
            'header' => [
                'left' => '',
                'center' => 'prev title next',
                'right' => '',
            ],
            'defaultDate' => $defaultDate,
        ]);
        return view('client.attendance.calendar', compact('batches', 'subdomainName', 'currnetYear','calendar','selectedYear','selectedBatch', 'allAttendanceDates','calendarYear'));
    }

    protected function getAttendanceByBatchByYearByClient($batch,$year,$clientId,$batchesCount){
        $attendanceCount = [];
        $result = [];
        $allAttendanceDates = [];
        if($batch > 0 && $year > 0){
            $allAttendance = ClientUserAttendance::where('client_batch_id','=', $batch)->whereYear('attendance_date', $year)->where('client_id', $clientId)->orderBy('attendance_date')->get();
        } else {
            $allAttendance = ClientUserAttendance::whereYear('attendance_date', $year)->where('client_id', $clientId)->orderBy('attendance_date')->get();
        }
        if(is_object($allAttendance) && false == $allAttendance->isEmpty()){
            foreach($allAttendance as $attendance){
                $studentCount = count(explode(',',$attendance->student_ids));
                $attendanceCount[$attendance->attendance_date]['present'] = $studentCount;
                $attendanceCount[$attendance->attendance_date]['absent'] = $batchesCount[$attendance->client_batch_id] - $studentCount;
                $allAttendanceDates[] = $attendance->attendance_date;
            }
        }

        $events = [];
        foreach($attendanceCount as $date => $arr) {
            $presentCnt = $arr['present'];
            $absentCnt = $arr['absent'];

            $events[] = \Calendar::event(
                        'Present - '.$presentCnt,
                        true,
                        new \DateTime($date),
                        new \DateTime($date.' +1 day'),
                        null,
                        // Add color and link on event
                        [
                            'color' => '#FAA732',
                        ]
                    );
            $events[] = \Calendar::event(
                        'Absent - '.$absentCnt,
                        true,
                        new \DateTime($date),
                        new \DateTime($date.' +1 day'),
                        null,
                        // Add color and link on event
                        [
                            'color' => '#FAA732',
                        ]
                    );
        }

        $result['allAttendanceDates'] = $allAttendanceDates;
        $result['events'] = $events;
        return $result;
    }
}