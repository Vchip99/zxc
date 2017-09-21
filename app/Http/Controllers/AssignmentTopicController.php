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

class AssignmentTopicController extends Controller
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
        'subject' => 'required',
        'topic' => 'required',
    ];

    /**
     * show all topics
     */
    protected function show(){
    	$topics = AssignmentTopic::where('lecturer_id', Auth::user()->id)->paginate();
    	return view('assignmentTopic.list', compact('topics'));
    }

    /**
     *  show create UI for topic
     */
    protected function create(){
    	$subjects = AssignmentSubject::where('lecturer_id', Auth::user()->id)->get();
    	$topic = new AssignmentTopic;
    	return view('assignmentTopic.create', compact('subjects', 'topic'));
    }

    /**
     *  store topic
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
            $topic = AssignmentTopic::addOrUpdateAssignmentTopic($request);
            if(is_object($topic)){
                DB::commit();
                return Redirect::to('manageAssignmentTopic')->with('message', 'Assignment Topic created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('manageAssignmentTopic');
    }


    /**
     * edit topic
     */
    protected function edit($id){
    	$topicId = InputSanitise::inputInt(json_decode($id));
    	if(isset($topicId)){
    		$topic = AssignmentTopic::find($topicId);
    		if(is_object($topic)){
    			$subjects = AssignmentSubject::where('lecturer_id', Auth::user()->id)->get();
    			return view('assignmentTopic.create', compact('subjects', 'topic'));
    		}
    	}
		return Redirect::to('manageAssignmentTopic');
    }

    /**
     * update topic
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        if(isset($topicId)){
            DB::beginTransaction();
            try
            {
                $topic = AssignmentTopic::addOrUpdateAssignmentTopic($request, true);
                if(is_object($topic)){
                    DB::commit();
                    return Redirect::to('manageAssignmentTopic')->with('message', 'Assignment Topic updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentTopic');
    }

    protected function delete(Request $request){
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        $topic = AssignmentTopic::find($topicId);
        if(is_object($topic)){
            DB::beginTransaction();
            try
            {
                $assignments = AssignmentQuestion::where('lecturer_id',Auth::user()->id)
                            ->where('assignment_topic_id',$topic->id)->get();
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
                $topic->delete();
                DB::commit();
                return Redirect::to('manageAssignmentTopic')->with('message', 'Assignment Topic deleted successfully!');

            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentTopic');
    }
}
