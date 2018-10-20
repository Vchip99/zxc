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
    protected $fillable = ['answer','assignment_question_id', 'student_id', 'lecturer_id', 'attached_link','is_student_created','student_dept_id'];

    /**
     *  add assignment answer
     */
    protected static function addAssignmentAnswer( Request $request){
        $answer = $request->get('answer');
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        $lecturerId   = InputSanitise::inputInt($request->get('lecturer_id'));
        $studentDeptId   = InputSanitise::inputInt($request->get('student_dept_id'));

        $assignmentAnswer = new static;
        $assignmentAnswer->answer = ($answer)?:'';
        $assignmentAnswer->assignment_question_id = $questionId;
        $assignmentAnswer->student_id = $studentId;
        $assignmentAnswer->lecturer_id = $lecturerId;
        $assignmentAnswer->student_dept_id = $studentDeptId;
        if( 2 == Auth::user()->user_type){
            $assignmentAnswer->is_student_created = 1;
        } else {
            $assignmentAnswer->is_student_created = 0;
        }

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

    protected static function deleteAnswersByUserIdByStudentDeptIds($userId,$removedDepts){
        $answers = static::where('lecturer_id', $userId)->whereIn('student_dept_id',$removedDepts)->get();
        if(is_object($answers) && false == $answers->isEmpty()){
            foreach($answers as $answer){
                $dir = dirname($answer->attached_link);
                InputSanitise::delFolder($dir);
                $answer->delete();
            }
        }
        return;
    }
}
