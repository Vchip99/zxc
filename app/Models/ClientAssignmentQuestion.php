<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;

class ClientAssignmentQuestion extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question', 'client_assignment_subject_id', 'client_assignment_topic_id', 'attached_link','client_id'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateAssignment( Request $request, $isUpdate=false){
    	$question = $request->get('question');
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $topicId   = InputSanitise::inputInt($request->get('topic'));
        $assignmentId   = InputSanitise::inputInt($request->get('assignment_id'));
        $clientId = Auth::guard('client')->user()->id;

        if( $isUpdate && isset($assignmentId)){
            $assignment = static::find($assignmentId);
            if(!is_object($assignment)){
            	return Redirect::to('manageAssignment');
            }
        } else {
            $assignment = new static;
        }

        $assignment->question = $question;
        $assignment->client_assignment_subject_id = $subjectId;
        $assignment->client_assignment_topic_id = $topicId;
        $assignment->client_id = $clientId;

        if( $request->exists('attached_link')){
	        $attachmentFolderPath = "clientAssignmentStorage/".$clientId."/topicId-".$topicId;
	        if(!is_dir($attachmentFolderPath)){
	        	mkdir($attachmentFolderPath, 0777, true);
	        }
	     	$attachedLinkFile = $request->file('attached_link')->getClientOriginalName();
	        $attachedLinkFilePath = $attachmentFolderPath."/".$attachedLinkFile;
	        if(file_exists($attachedLinkFilePath)){
	        	unlink($attachedLinkFilePath);
	        }
	        $request->file('attached_link')->move($attachmentFolderPath, $attachedLinkFile);
	        $assignment->attached_link = $attachedLinkFilePath;
        }
        $assignment->save();
        return $assignment;
    }

    public function subject(){
        return $this->belongsTo(ClientAssignmentSubject::class, 'client_assignment_subject_id');
    }

    public function topic(){
        return $this->belongsTo(ClientAssignmentTopic::class, 'client_assignment_topic_id');
    }

    protected static function checkAssignmentExist(Request $request){
    	$result = [];
    	$query = static::where('client_assignment_subject_id', $request->subject_id)
    			->where('client_assignment_topic_id', $request->topic_id)
    			->where('client_id', Auth::guard('client')->user()->id)
    			->first();
    	if(is_object($query)){
    		$result['status'] = 'true';
    		$result['id'] = $query->id;
    	} else {
    		$result['status'] = 'false';
    	}
    	return $result;
    }

    protected static function deleteClientAssignmentQuestionByClientId($clientId){
        $assignments = static::where('client_id', $clientId)->get();
        if(is_object($assignments) && false == $assignments->isEmpty()){
            foreach($assignments as $assignment){
                if(file_exists($assignment->attached_link)){
                    unlink($assignment->attached_link);
                }
                $assignment->delete();
            }
        }
        return;
    }
}
