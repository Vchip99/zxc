<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Models\AssignmentTopic;
use App\Models\AssignmentSubject;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentQuestion;
use App\Libraries\InputSanitise;

class AssignmentSubjectController extends Controller
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
    protected $validateCreateSubject = [
        'subject' => 'required|string',
        'year'  => 'required'
    ];

    /**
     * show all subjects
     */
    protected function show(){
    	$subjects = AssignmentSubject::where('lecturer_id', Auth::user()->id)->paginate();
    	return view('assignmentSubject.list', compact('subjects'));
    }

    /**
     * show UI for create subject
     */
    protected function create(){
    	$subject = new AssignmentSubject;
    	return view('assignmentSubject.create', compact('subject'));
    }

    /**
     *  store subject
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subject = AssignmentSubject::addOrUpdateAssignmentSubject($request);
            if(is_object($subject)){
                DB::commit();
                return Redirect::to('manageAssignmentSubject')->with('message', 'Assignment Subject created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('manageAssignmentSubject');
    }

    /**
     * edit subject
     */
    protected function edit($id){
    	$subjectId = InputSanitise::inputInt(json_decode($id));
    	if(isset($subjectId)){
    		$subject = AssignmentSubject::find($subjectId);
    		if(is_object($subject)){
    			return view('assignmentSubject.create', compact('subject'));
    		}
    	}
		return Redirect::to('manageAssignmentSubject');
    }

    /**
     * update subject
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        if(isset($subjectId)){
            DB::beginTransaction();
            try
            {
                $subject = AssignmentSubject::addOrUpdateAssignmentSubject($request, true);
                if(is_object($subject)){
                    DB::commit();
                    return Redirect::to('manageAssignmentSubject')->with('message', 'Assignment Subject updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentSubject');
    }

    protected function getAssignmentSubjectsByYear(Request $request){
        return AssignmentSubject::getAssignmentSubjectsByYear($request->year,$request->lecturer,$request->department);
    }

    protected function getAssignmentSubjectsOfGivenAssignmentByLecturer(Request $request){
        return AssignmentSubject::getAssignmentSubjectsOfGivenAssignmentByLecturer($request);
    }

    protected function delete(Request $request){
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $subject = AssignmentSubject::find($subjectId);
        if(is_object($subject)){
            DB::beginTransaction();
            try
            {
                $topics = AssignmentTopic::where('lecturer_id',Auth::user()->id)->where('assignment_subject_id',$subject->id)->get();
                if(is_object($topics) && false == $topics->isEmpty()){
                    foreach($topics as $topic){
                        $assignments = AssignmentQuestion::where('lecturer_id',Auth::user()->id)->where('assignment_topic_id',$topic->id)->get();
                        if(is_object($assignments) && false == $assignments->isEmpty()){
                            foreach($assignments as $assignment){
                                $answers = AssignmentAnswer::where('assignment_question_id', $assignment->id)->where('lecturer_id',Auth::user()->id)->get();
                                if(is_object($answers) && false == $answers->isEmpty()){
                                    foreach($answers as $answer){
                                        $answer->delete();
                                    }
                                }
                                $assignment->delete();
                            }
                        }
                    }
                    $topic->delete();
                }
                $subject->delete();
                DB::commit();
                return Redirect::to('manageAssignmentSubject')->with('message', 'Assignment Subject deleted successfully!');

            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentSubject');
    }
}
