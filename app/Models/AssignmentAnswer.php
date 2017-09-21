<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\AssignmentSubject;
use App\Models\AssignmentTopic;
use App\Models\User;

class AssignmentAnswer extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['answer','lecturer_comment','assignment_question_id', 'student_id', 'lecturer_id', 'attached_link'];

    /**
     *  add assignment answer
     */
    protected static function addAssignmentAnswer( Request $request){
        $answer = $request->get('answer');
        $lecturerComment = $request->get('lecturer_comment');
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        $lecturerId   = InputSanitise::inputInt($request->get('lecturer_id'));

        $assignmentAnswer = new static;
        $assignmentAnswer->answer = $answer?:'';
        $assignmentAnswer->lecturer_comment = $lecturerComment?:'';
        $assignmentAnswer->assignment_question_id = $questionId;
        $assignmentAnswer->student_id = $studentId;
        $assignmentAnswer->lecturer_id = $lecturerId;

        if($request->exists('attached_link')){
	        $attachmentFolderPath = "assignmentStorage/studentId-".$studentId."/assignmentId-".$questionId;
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
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher(){
        return $this->belongsTo(User::class, 'lecturer_id');
    }
}
