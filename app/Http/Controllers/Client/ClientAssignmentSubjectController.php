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

class ClientAssignmentSubjectController extends ClientBaseController
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
    protected $validateSubject = [
        'subject' => 'required',
    ];

    protected function show($subdomainName){
        $subjects = ClientAssignmentSubject::where('client_id', Auth::guard('client')->user()->id)->paginate();
        return view('client.assignmentSubject.list', compact('subjects','subdomainName'));
    }

    /**
     *  create assignment subject
     */
    protected function create($subdomainName){
        $subject = new ClientAssignmentSubject;
        return view('client.assignmentSubject.create', compact('subject', 'subdomainName'));
    }

    /**
     *  store assignment subject
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subject = ClientAssignmentSubject::addOrUpdateAssignmentSubject($request);
            if(is_object($subject)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignmentSubject')->with('message', 'Assignment Subject created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAssignmentSubject');
    }

    /**
     *  edit assignment subject
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $subject = ClientAssignmentSubject::find($id);

            if(is_object($subject)){
                return view('client.assignmentSubject.create', compact('subject', 'subdomainName'));
            }
        }
        return Redirect::to('manageAssignmentSubject');
    }

    /**
     *  update assignment subject
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        if(isset($subjectId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $subject = ClientAssignmentSubject::addOrUpdateAssignmentSubject($request, true);
                if(is_object($subject)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageAssignmentSubject')->with('message', 'Assignment Subject updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentSubject');
    }

    protected function getAssignmentSubjectsByCourse(Request $request){
        return ClientAssignmentSubject::getAssignmentSubjectsByClient();
    }

    protected function delete(Request $request){
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));

        $subject = ClientAssignmentSubject::find($subjectId);
        if(is_object($subject)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $loginUser = Auth::guard('client')->user();
                $topics = ClientAssignmentTopic::where('client_assignment_subject_id', $subject->id)->where('client_id',$loginUser->id)->get();
                if(is_object($topics) && false == $topics->isEmpty()){
                    foreach($topics as $topic){
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
                    }
                }
                $subject->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAssignmentSubject')->with('message', 'Subject deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageAssignmentSubject');
    }

}