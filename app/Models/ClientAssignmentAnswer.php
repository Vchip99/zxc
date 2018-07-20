<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\Client;
use App\Models\Clientuser;

class ClientAssignmentAnswer extends Model
{
	protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['answer','client_assignment_question_id', 'student_id', 'client_id', 'attached_link','is_student_created'];

    /**
     *  add assignment answer
     */
    protected static function addAssignmentAnswer( Request $request){
        $answer = $request->get('answer');
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        $clientId   = InputSanitise::inputInt($request->get('client_id'));

        $assignmentAnswer = new static;
        $assignmentAnswer->answer = $answer?:'';
        $assignmentAnswer->client_assignment_question_id = $questionId;
        $assignmentAnswer->student_id = $studentId;
        $assignmentAnswer->client_id = $clientId;
        if(is_object(Auth::guard('client')->user())){
            $assignmentAnswer->is_student_created = 0;
        } else {
            $assignmentAnswer->is_student_created = 1;
        }

        if($request->exists('attached_link')){
	        $attachmentFolderPath = "clientAssignmentStorage/".$clientId."/studentId-".$studentId."/assignmentId-".$questionId;
	        if(!is_dir($attachmentFolderPath)){
	        	mkdir($attachmentFolderPath, 0777, true);
	        }
	     	$attachedLinkFile = $request->file('attached_link')->getClientOriginalName();
	        $attachedLinkFilePath = $attachmentFolderPath."/".$attachedLinkFile;
	        if(file_exists($attachedLinkFilePath)){
	        	unlink($attachedLinkFilePath);
	        }
	        $request->file('attached_link')->move($attachmentFolderPath, $attachedLinkFile);
	        $assignmentAnswer->attached_link = $attachedLinkFilePath;
        }
        $assignmentAnswer->save();
        return $assignmentAnswer;
    }

    public function student(){
        return $this->belongsTo(Clientuser::class, 'student_id');
    }

    public function teacher(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    protected static function deleteClientAssignmentAnswerByClientId($clientId){
        $assignmentAnswers = static::where('client_id', $clientId)->get();
        if(is_object($assignmentAnswers) && false == $assignmentAnswers->isEmpty()){
            foreach($assignmentAnswers as $assignmentAnswer){
                if(file_exists($assignmentAnswer->attached_link)){
                    unlink($assignmentAnswer->attached_link);
                }
                $assignmentAnswer->delete();
            }
        }
        return;
    }

    protected static function deleteClientAssignmentAnswerByClientIdByUserId($clientId,$userId){
        $assignmentAnswers = static::where('client_id', $clientId)->where('student_id', $userId)->get();
        if(is_object($assignmentAnswers) && false == $assignmentAnswers->isEmpty()){
            foreach($assignmentAnswers as $assignmentAnswer){
                if(file_exists($assignmentAnswer->attached_link)){
                    unlink($assignmentAnswer->attached_link);
                }
                $assignmentAnswer->delete();
            }
        }
        return;
    }

    protected static function getClientAssignmentAnswersByAssignmentIdByClientId($assignmentId,$clientId){
        return static::where('client_assignment_question_id', $assignmentId)->where('client_id', $clientId)->get();
    }
}