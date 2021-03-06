<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentAnswer;
use App\Models\ClientBatch;

class ClientAssignmentController extends ClientBaseController
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
    protected $validateAssignment = [
        'subject' => 'required|integer',
        'topic' => 'required',
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
        $assignments = ClientAssignmentQuestion::where('client_id', $clientId)->paginate();
        return view('client.assignment.list', compact('assignments', 'subdomainName','loginUser'));
    }

    /**
     *  create assignment
     */
    protected function create($subdomainName,Request $request){
        if($subdomainName){
            InputSanitise::checkClientImagesDirForCkeditor($subdomainName);
        }
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
        $topics = [];
        $subjects = [];
        $assignment = new ClientAssignmentQuestion;
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.assignment.create', compact('subjects', 'topics', 'assignment', 'subdomainName', 'batches','loginUser'));
    }

    /**
     *  store assignment
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateAssignment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        if(empty($request->get('question')) && empty($request->file('attached_link'))){
            return Redirect::to('manageAssignment')->with('message', 'please enter questions or select attachment.');
        }

        DB::connection('mysql2')->beginTransaction();
        try
        {
            $assignment = ClientAssignmentQuestion::addOrUpdateAssignment($request);
            if(is_object($assignment)){
                if($assignment->client_batch_id > 0){
                    $studentIds = [];
                    $batch = ClientBatch::find($assignment->client_batch_id);
                    if(is_object($batch)){
                        $studentIds = explode(',', $batch->student_ids);
                    }
                    if(count($studentIds) > 0){
                        $users = Clientuser::getStudentsByIds($studentIds);
                        $this->setAssignmentCount($users);
                    }
                } else {
                    $users = Clientuser::getAllStudentsByClientId($assignment->client_id);
                    $this->setAssignmentCount($users);
                }
                $this->sendAssignmentMessage($assignment);
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignment')->with('message', 'Assignment created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAssignment');
    }

    /**
     * edit assignment
     */
    protected function edit($subdomainName,Request $request,$id){
        if($subdomainName){
            InputSanitise::checkClientImagesDirForCkeditor($subdomainName);
        }
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
            $assignment = ClientAssignmentQuestion::find($id);
            if(is_object($assignment)){
                $topics = ClientAssignmentTopic::getAssignmentTopicsBySubject($assignment->client_assignment_subject_id);
                $subjects = ClientAssignmentSubject::getAssignmentSubjectsByBatchId($assignment->client_batch_id);
                $batches = ClientBatch::getBatchesByClientId($assignment->client_id);
                return view('client.assignment.create', compact('subjects','topics','assignment','subdomainName','batches','loginUser'));
            }
        }
        return Redirect::to('manageAssignment');
    }

    /**
     * update assignment
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateAssignment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        if(empty($request->get('question')) && empty($request->get('attached_link'))){
            return Redirect::to('manageAssignment')->with('message', 'please enter questions or select attachment.');
        }
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        if(isset($assignmentId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $assignment = ClientAssignmentQuestion::addOrUpdateAssignment($request, true);
                if(is_object($assignment)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageAssignment')->with('message', 'Assignment updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignment');
    }

    protected function checkAssignmentExist(Request $request){
        return ClientAssignmentQuestion::checkAssignmentExist($request);
    }

    protected function getAssignmentSubjectsByCourse(Request $request){
        return ClientAssignmentSubject::getAssignmentSubjectsByCourse($request->institute_course_id);
    }

    protected function studentsAssignment($subdomainName,Request $request){
        $assignment = '';
        $assignmentSubjects = [];
        $assignmentTopics = [];
        $assignmentUsers = [];
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

        $selectedAssignmentBatch = Session::get('client_selected_assignment_batch');
        $selectedAssignmentSubject  = Session::get('client_selected_assignment_subject');
        $selectedAssignmentTopic = Session::get('client_selected_assignment_topic');
        $selectedAssignmentStudent = Session::get('client_selected_assignment_student');
        if($selectedAssignmentBatch){
            $assignmentSubjects = ClientAssignmentSubject::getAssignmentSubjectsByBatchId($selectedAssignmentBatch);
            $assignmentUsers = Clientuser::searchStudentForAssignment($selectedAssignmentBatch);
        }

        if(!empty($selectedAssignmentSubject)){
            $assignmentTopics = ClientAssignmentTopic::getAssignmentTopicsBySubject($selectedAssignmentSubject);
        }
        if(!empty($selectedAssignmentTopic)){
            $assignment = ClientAssignmentQuestion::where('client_id', $clientId)
                    ->where('client_assignment_topic_id', $selectedAssignmentTopic)->first();
        }
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.studentAssignment.studentsAssignment', compact('assignmentSubjects', 'assignmentTopics', 'assignmentUsers', 'selectedAssignmentSubject', 'selectedAssignmentTopic', 'selectedAssignmentStudent', 'assignment', 'subdomainName', 'batches', 'selectedAssignmentBatch','loginUser'));
    }

    protected function searchStudentForAssignment(Request $request){
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        return Clientuser::searchStudentForAssignment($batchId);
    }

    protected function getAssignmentByTopicForStudent(Request $request){
        $results = [];
        $batch = $request->batch;
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $assignment = ClientAssignmentQuestion::where('client_id', $clientId)
                    ->where('client_assignment_topic_id', $request->topic)->first();
        if(is_object($assignment)){
            $results['id'] = $assignment->id;
            $results['question'] = mb_strimwidth($assignment->question, 0, 400, "...");
            if(0 == $assignment->client_batch_id || empty($assignment->client_batch_id)){
                $results['batch'] = 'All';
            } else {
                $results['batch'] = $assignment->batch->name;
            }
            $results['subject'] = $assignment->subject->name;
            $results['topic'] = $assignment->topic->name;

            Session::set('client_selected_assignment_batch', $batch);
            Session::set('client_selected_assignment_subject', $assignment->client_assignment_subject_id);
            Session::set('client_selected_assignment_topic', $assignment->client_assignment_topic_id);
            Session::set('client_selected_assignment_student', $request->student);
        }
        return $results;
    }

    protected function assignmentRemark($subdomainName,Request $request, $id, $studentId){
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
        $studentId = InputSanitise::inputInt(json_decode($studentId));
        $assignment = ClientAssignmentQuestion::find($id);
        $student = Clientuser::find($studentId);
        $answers = ClientAssignmentAnswer::where('client_id', $student->client_id)->where('student_id', $student->id)->where('client_assignment_question_id', $assignment->id)->get();
        return view('client.studentAssignment.assignmentRemark', compact('assignment', 'answers','student', 'subdomainName','loginUser'));
    }

    protected function createAssignmentRemark(Request $request){
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        if(empty($request->get('answer')) && false == $request->exists('attached_link')){
            return Redirect::to('studentsAssignment')->with('message', 'please enter remark or select attachment.');
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientAssignmentAnswer::addAssignmentAnswer($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('assignmentRemark/'.$questionId.'/'.$studentId)->with('message', 'Assignment updated successfully!');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('studentsAssignment');
    }

    protected function delete(Request $request){
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        $assignment = ClientAssignmentQuestion::find($assignmentId);
        if(is_object($assignment)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $loginUser = InputSanitise::getLoginUserByGuardForClient();
                if($assignment->created_by > 0 && $loginUser->id != $assignment->created_by){
                    return Redirect::to('studentsAssignment');
                }
                if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                    return Redirect::to('studentsAssignment');
                }
                $resultArr = InputSanitise::getClientIdAndCretedBy();
                $clientId = $resultArr[0];
                $answers = ClientAssignmentAnswer::where('client_assignment_question_id', $assignment->id)->where('client_id',$clientId)->get();
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
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignment')->with('message', 'Assignment deleted successfully!');

            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignment');
    }

    protected function setAssignmentCount($users){
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if(0 == $user->unchecked_assignments || empty($user->unchecked_assignments)){
                    $user->unchecked_assignments = 1;
                } else {
                    $user->unchecked_assignments++;
                }
                $user->save();
            }
        }
        return;
    }

    protected function sendAssignmentMessage($assignment){
        if('client' == InputSanitise::getCurrentGuard()){
            $client = Auth::guard('client')->user();
            $sendSmsStatus = $client->assignment_sms;
        } else {
            $clientUser = Auth::guard('clientuser')->user();
            $client = $clientUser->client;
            $sendSmsStatus = $client->assignment_sms;
        }
        if(Client::None != $sendSmsStatus){
            $allBatchStudents = [];
            if($assignment->client_batch_id > 0){
                $clientBatch = ClientBatch::where('client_id',$assignment->client_id)->where('id',$assignment->client_batch_id)->first();
                if(is_object($clientBatch)){
                    if(!empty($clientBatch->student_ids)){
                        $allBatchStudents = explode(',', $clientBatch->student_ids);
                    }
                }
                $batchName = $clientBatch->name;
            } else {
                $batchName = 'All';
            }
            InputSanitise::sendAssignmentSms($allBatchStudents,$sendSmsStatus,$assignment->client_batch_id,$batchName,$assignment->topic->name,$client);
        }
        return;
    }
}