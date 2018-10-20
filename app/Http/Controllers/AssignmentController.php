<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentAnswer;
use App\Models\CollegeSubject;
use App\Models\AssignmentTopic;
use App\Models\CollegeDept;
use App\Models\User;
use App\Libraries\InputSanitise;

class AssignmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCreateAssignment = [
        'subject' => 'required',
        'topic' => 'required',
        'question' => 'required'
    ];

    /**
     * show all assignment
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $assignmentTeachers = [];
        $departments = [];
        $allSubjects = [];
        $allTopics = [];
        $loginUser = Auth::user();
    	$assignments = AssignmentQuestion::where('college_id', $loginUser->college_id)->paginate();

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
           $assignments = AssignmentQuestion::getAssignmentsByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $assignments = AssignmentQuestion::getAssignmentsByCollegeIdWithPagination($loginUser->college_id);
        }

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $deptIds = explode(',',$loginUser->assigned_college_depts);
            $departments = CollegeDept::getDepartmentsByCollegeIdByDeptIds($loginUser->college_id,$deptIds);
        } else {
            $departments = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        }

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeId($loginUser->college_id);
        }
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $allTopics[$topic->id] = $topic->name;
            }
        }

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        }
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id] = $subject->name;
            }
        }

    	return view('assignment.list', compact('assignments', 'assignmentTeachers', 'departments','allSubjects','allTopics'));
    }

    /**
     *  show create UI for topic
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$topics = [];
        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsByUser($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
        }
    	$assignment = new AssignmentQuestion;
    	return view('assignment.create', compact('subjects', 'assignment', 'topics'));
    }

    /**
     *  store assignment
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateAssignment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        DB::beginTransaction();
        try
        {
            $assignment = AssignmentQuestion::addOrUpdateAssignment($request);
            if(is_object($assignment)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageAssignment')->with('message', 'Assignment created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('college/'.$collegeUrl.'/manageAssignment');
    }

    /**
     * edit assignment
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$assignmentId = InputSanitise::inputInt(json_decode($id));
    	if(isset($assignmentId)){
    		$assignment = AssignmentQuestion::find($assignmentId);
    		if(is_object($assignment)){
                $loginUser = Auth::user();
                $subjects = '';
                if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                    $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdByAssignedDeptsForList($loginUser->college_id);
                } else {
                    $topics = AssignmentTopic::getAssignmentTopicsByCollegeId($loginUser->college_id);
                }

                if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                    $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
                } else {
                    $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
                }

    			return view('assignment.create', compact('subjects', 'assignment', 'topics'));
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageAssignment');
    }

    /**
     * update assignment
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateAssignment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        if(isset($assignmentId)){
            DB::beginTransaction();
            try
            {
                $assignment = AssignmentQuestion::addOrUpdateAssignment($request, true);
                if(is_object($assignment)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageAssignment')->with('message', 'Assignment updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageAssignment');
    }

    protected function getAssignmentTopics(Request $request){
    	return AssignmentTopic::getAssignmentTopics($request->id);
    }

    protected function getAssignmentByTopic(Request $request){
        $allSubjects = [];
        $allTopics = [];
        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        }
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id] = $subject->name;
            }
        }
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeId($loginUser->college_id);
        }
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $allTopics[$topic->id] = $topic->name;
            }
        }

        $assignment = AssignmentQuestion::getAssignmentByTopic($request->topic);
        $result = [];
        if(is_object($assignment)){
            $result['id'] = $assignment->id;
            $result['question'] = $assignment->question;
            $result['attached_link'] = basename($assignment->attached_link);
            $result['topic'] = $allTopics[$assignment->assignment_topic_id];
            $result['subject'] = $allSubjects[$assignment->college_subject_id];
            $result['lecturer_id'] = $assignment->lecturer_id;
        }
        return $result;
    }

    protected function getAssignments(Request $request){
        $allSubjects = [];
        $allTopics = [];
        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        }
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id] = $subject->name;
            }
        }
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeId($loginUser->college_id);
        }
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $allTopics[$topic->id] = $topic->name;
            }
        }
        $result = [];
        $assignments = AssignmentQuestion::getAssignments($request);
        if(is_object($assignments) && false == $assignments->isEmpty()){
            foreach($assignments as $index => $assignment){
                $result[$index+1]['id'] = $assignment->id;
                $result[$index+1]['question'] = $assignment->question;
                $result[$index+1]['attached_link'] = basename($assignment->attached_link);
                $result[$index+1]['topic'] = $allTopics[$assignment->assignment_topic_id];
                $result[$index+1]['subject'] = $allSubjects[$assignment->college_subject_id];
                $result[$index+1]['lecturer_id'] = $assignment->lecturer_id;
            }
        }
        return $result;
    }

    protected function getAssignmentByTopicForStudent(Request $request){
        $allSubjects = [];
        $allTopics = [];
        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        }
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id] = $subject->name;
            }
        }
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeId($loginUser->college_id);
        }
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $allTopics[$topic->id] = $topic->name;
            }
        }
        $assignment = AssignmentQuestion::getAssignmentByTopic($request->topic);
        $result = [];
        if(is_object($assignment)){
            $result['id'] = $assignment->id;
            $result['question'] = $assignment->question;
            $result['attached_link'] = basename($assignment->attached_link);
            $result['topic'] = $allTopics[$assignment->assignment_topic_id];
            $result['subject'] = $allSubjects[$assignment->college_subject_id];
            Session::put('selected_assignment_year', $request->year);
            Session::put('selected_assignment_subject', $request->subject);
            Session::put('selected_assignment_topic', $request->topic);
            Session::put('selected_assignment_student', $request->student);
            Session::put('selected_assignment_department', $request->department);
        }
        return $result;
    }

    protected function checkAssignmentIsExist(Request $request){
        return AssignmentQuestion::checkAssignmentIsExist($request);
    }

    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        $assignment = AssignmentQuestion::find($assignmentId);
        if(is_object($assignment)){
            DB::beginTransaction();
            try
            {
                if(Auth::user()->id == $assignment->lecturer_id){
                    $answers = AssignmentAnswer::where('assignment_question_id', $assignment->id)->where('lecturer_id',Auth::user()->id)->get();
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
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageAssignment')->with('message', 'Assignment deleted successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageAssignment');
    }
}
