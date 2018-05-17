<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentSubject;
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
    protected function show(){
        $assignmentTeachers = [];
        $departments = [];
        $loginUser = Auth::user();
    	$assignments = AssignmentQuestion::where('lecturer_id', $loginUser->id)->paginate();
        if(User::Hod == $loginUser->user_type){
            $assignmentTeachers = User::getTeachers();
        }
        if(User::Directore == $loginUser->user_type){
            $departments = CollegeDept::where('college_id', $loginUser->college_id)->get();
        }
    	return view('assignment.list', compact('assignments', 'assignmentTeachers', 'departments'));
    }

    /**
     *  show create UI for topic
     */
    protected function create(){
    	$topics = [];
    	// $subjects = AssignmentSubject::where('lecturer_id', Auth::user()->id)->get();
        $subjects = [];
    	$assignment = new AssignmentQuestion;
    	return view('assignment.create', compact('subjects', 'assignment', 'topics'));
    }

    /**
     *  store assignment
     */
    protected function store(Request $request){
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
                return Redirect::to('manageAssignment')->with('message', 'Assignment created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('manageAssignment');
    }

    /**
     * edit assignment
     */
    protected function edit($id){
    	$assignmentId = InputSanitise::inputInt(json_decode($id));
    	if(isset($assignmentId)){
    		$assignment = AssignmentQuestion::find($assignmentId);
    		if(is_object($assignment)){
                $loginUser = Auth::user();
                $subjects = '';
                if(User::Lecturer == $loginUser->user_type){
    			    $subjects = AssignmentSubject::where('lecturer_id', $loginUser->id)->get();
                } else if(User::Hod == $loginUser->user_type){
                    $subjects = AssignmentSubject::where('college_dept_id', $loginUser->college_dept_id)->get();
                } else if(User::Directore == $loginUser->user_type){
                    $subjects = AssignmentSubject::where('college_id', $loginUser->college_id)->get();
                }
    			$topics = AssignmentTopic::getAssignmentTopics($assignment->assignment_subject_id);
    			return view('assignment.create', compact('subjects', 'assignment', 'topics'));
    		}
    	}
		return Redirect::to('manageAssignment');
    }

    /**
     * update assignment
     */
    protected function update(Request $request){
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
                    return Redirect::to('manageAssignment')->with('message', 'Assignment updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignment');
    }

    protected function getAssignmentTopics(Request $request){
    	return AssignmentTopic::getAssignmentTopics($request->id);
    }

    protected function getAssignmentByTopic(Request $request){
        $assignment = AssignmentQuestion::getAssignmentByTopic($request->topic);
        $result = [];
        if(is_object($assignment)){
            $result['id'] = $assignment->id;
            $result['question'] = $assignment->question;
            $result['attached_link'] = basename($assignment->attached_link);
            $result['topic'] = $assignment->topic->name;
            $result['subject'] = $assignment->subject->name;
            $result['lecturer_id'] = $assignment->lecturer_id;
        }
        return $result;
    }

    protected function getAssignments(Request $request){
        $result = [];
        $assignments = AssignmentQuestion::getAssignments($request);
        if(is_object($assignments) && false == $assignments->isEmpty()){
            foreach($assignments as $index => $assignment){
                $result[$index+1]['id'] = $assignment->id;
                $result[$index+1]['question'] = $assignment->question;
                $result[$index+1]['attached_link'] = basename($assignment->attached_link);
                $result[$index+1]['topic'] = $assignment->topic->name;
                $result[$index+1]['subject'] = $assignment->subject->name;
                $result[$index+1]['lecturer_id'] = $assignment->lecturer_id;
            }
        }
        return $result;
    }

    protected function getAssignmentByTopicForStudent(Request $request){
        $assignment = AssignmentQuestion::getAssignmentByTopic($request->topic);
        $result = [];
        if(is_object($assignment)){
            $result['id'] = $assignment->id;
            $result['question'] = $assignment->question;
            $result['attached_link'] = basename($assignment->attached_link);
            $result['topic'] = $assignment->topic->name;
            $result['subject'] = $assignment->subject->name;
            Session::put('selected_assignment_year', $assignment->year);
            Session::put('selected_assignment_subject', $assignment->assignment_subject_id);
            Session::put('selected_assignment_topic', $assignment->assignment_topic_id);
            Session::put('selected_assignment_student', $request->student);
        }
        return $result;
    }

    protected function checkAssignmentIsExist(Request $request){
        return AssignmentQuestion::checkAssignmentIsExist($request);
    }

    protected function delete(Request $request){
        $assignmentId = InputSanitise::inputInt($request->get('assignment_id'));
        $assignment = AssignmentQuestion::find($assignmentId);
        if(is_object($assignment)){
            DB::beginTransaction();
            try
            {
                $answers = AssignmentAnswer::where('assignment_question_id', $assignment->id)->where('lecturer_id',Auth::user()->id)->get();
                if(is_object($answers) && false == $answers->isEmpty()){
                    foreach($answers as $answer){
                        $answer->delete();
                    }
                }
                $assignment->delete();
                DB::commit();
                return Redirect::to('manageAssignment')->with('message', 'Assignment deleted successfully!');

            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignment');
    }
}
