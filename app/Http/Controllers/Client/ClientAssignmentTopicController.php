<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentAnswer;

class ClientAssignmentTopicController extends ClientBaseController
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
    protected $validateTopic = [
        'subject' => 'required|integer',
        'topic' => 'required',
    ];

    protected function show(){
        $topics = ClientAssignmentTopic::where('client_id', Auth::guard('client')->user()->id)->paginate();
        return view('client.assignmentTopic.list', compact('topics'));
    }

    /**
     *  create assignment topic
     */
    protected function create(){
        $topic = new ClientAssignmentTopic;
        $subjects = ClientAssignmentSubject::getAssignmentSubjectsByClient();
        return view('client.assignmentTopic.create', compact('topic', 'subjects'));
    }

    /**
     *  store assignment topic
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateTopic);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $topic = ClientAssignmentTopic::addOrUpdateAssignmentTopic($request);
            if(is_object($topic)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignmentTopic')->with('message', 'Assignment Topic created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAssignmentTopic');
    }

    /**
     *  edit assignment subject
     */
    protected function edit($subdomain, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $topic = ClientAssignmentTopic::find($id);
            if(is_object($topic)){
                $subjects = ClientAssignmentSubject::getAssignmentSubjectsByClient();
                return view('client.assignmentTopic.create', compact('topic', 'subjects'));
            }
        }
        return Redirect::to('manageAssignmentTopic');
    }

    /**
     *  update assignment subject
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateTopic);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        if(isset($topicId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $topic = ClientAssignmentTopic::addOrUpdateAssignmentTopic($request, true);
                if(is_object($topic)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageAssignmentTopic')->with('message', 'Assignment Topic updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentTopic');
    }

    protected function getAssignmentTopicsBySubject(Request $request){
        return ClientAssignmentTopic::getAssignmentTopicsBySubject($request->subject_id);
    }

    protected function delete(Request $request){
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        $topic = ClientAssignmentTopic::find($topicId);
        if(is_object($topic)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $loginUser = Auth::guard('client')->user();
                $assignments = ClientAssignmentQuestion::where('client_assignment_topic_id', $topic->id)->where('client_id',$loginUser->id)->get();
                if(is_object($assignments) && false == $assignments->isEmpty()){
                    foreach($assignments as $assignment){
                        $answers = ClientAssignmentAnswer::where('client_assignment_question_id', $assignment->id)->where('client_id',$loginUser->id)->get();
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
                $topic->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignmentTopic')->with('message', 'Topic deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentTopic');
    }

}