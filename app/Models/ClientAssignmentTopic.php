<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientAssignmentSubject;

class ClientAssignmentTopic extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_assignment_subject_id', 'client_id'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateAssignmentTopic( Request $request, $isUpdate=false){
        $topicName = InputSanitise::inputString($request->get('topic'));
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $topicId   = InputSanitise::inputInt($request->get('topic_id'));

        if( $isUpdate && isset($topicId)){
            $topic = static::find($topicId);
            if(!is_object($topic)){
            	return 'false';
            }
        } else{
            $topic = new static;
        }
        $topic->name = $topicName;
        $topic->client_assignment_subject_id = $subjectId;
        $topic->client_id = Auth::guard('client')->user()->id;
        $topic->save();
        return $topic;
    }

    public function subject(){
        return $this->belongsTo(ClientAssignmentSubject::class, 'client_assignment_subject_id');
    }

    protected static function getAssignmentTopicsBySubject($subjectId){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            return static::where('client_id', $loginClient->id)->where('client_assignment_subject_id', $subjectId)->get();
        } else {
            return static::where('client_id', Auth::guard('clientuser')->user()->client_id)->where('client_assignment_subject_id', $subjectId)->get();
        }
    }

    protected static function deleteClientAssignmentTopicByClientId($clientId){
        $topics = static::where('client_id', $clientId)->get();
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $topic->delete();
            }
        }
        return;
    }
}
