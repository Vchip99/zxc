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
use App\Models\ClientBatch;

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
        // $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateSubject = [
        'subject' => 'required',
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
        $subjects = ClientAssignmentSubject::where('client_id', $clientId)->paginate();
        return view('client.assignmentSubject.list', compact('subjects','subdomainName','loginUser'));
    }

    /**
     *  create assignment subject
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
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $subject = new ClientAssignmentSubject;
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.assignmentSubject.create', compact('subject', 'subdomainName', 'batches','loginUser'));
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
            $subject = ClientAssignmentSubject::find($id);
            if(is_object($subject)){
                $batches = ClientBatch::getBatchesByClientId($subject->client_id);
                return view('client.assignmentSubject.create', compact('subject','subdomainName','batches','loginUser'));
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
                $loginUser = InputSanitise::getLoginUserByGuardForClient();
                if($subject->created_by > 0 && $loginUser->id != $subject->created_by){
                    return Redirect::to('manageAssignmentSubject');
                }
                if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                    return Redirect::to('manageAssignmentSubject');
                }
                $topics = ClientAssignmentTopic::getAssignmentTopicsBySubjectIdByClientId($subject->id,$subject->client_id);
                if(is_object($topics) && false == $topics->isEmpty()){
                    foreach($topics as $topic){
                        $assignments = ClientAssignmentQuestion::getClientAssignmentQuestionsByTopicIdByClientId($topic->id,$topic->client_id);
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

    protected function getAssignmentSubjectsByBatchId(Request $request){
        $clientBatchId = InputSanitise::inputInt($request->get('batch_id'));
        return ClientAssignmentSubject::getAssignmentSubjectsByBatchId($clientBatchId);
    }

}