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
        // $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateTopic = [
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
        $topics = ClientAssignmentTopic::where('client_id', $clientId)->paginate();
        return view('client.assignmentTopic.list', compact('topics','subdomainName','loginUser'));
    }

    /**
     *  create assignment topic
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
        $topic = new ClientAssignmentTopic;
        $subjects = [];
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.assignmentTopic.create', compact('topic','subjects','subdomainName','batches','loginUser'));
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
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $topic = ClientAssignmentTopic::find($id);
            if(is_object($topic)){
                $subjects = ClientAssignmentSubject::getAssignmentSubjectsByBatchId($topic->client_batch_id);
                $batches = ClientBatch::getBatchesByClientId($topic->client_id);
                return view('client.assignmentTopic.create', compact('topic','subjects','subdomainName','batches','loginUser'));
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
                $loginUser = InputSanitise::getLoginUserByGuardForClient();
                if($topic->created_by > 0 && $loginUser->id != $topic->created_by){
                    return Redirect::to('manageAssignmentTopic');
                }
                if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                    return Redirect::to('manageAssignmentTopic');
                }
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