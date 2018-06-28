<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\AssignmentSubject;
use App\Models\AssignmentTopic;

class AssignmentQuestion extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question','assignment_subject_id','assignment_topic_id', 'attached_link', 'lecturer_id', 'college_id', 'college_dept_id', 'year'];

    /**
     *  add/update assignment
     */
    protected static function addOrUpdateAssignment( Request $request, $isUpdate=false){
        $question = $request->get('question');
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $topicId   = InputSanitise::inputInt($request->get('topic'));
        $year   = InputSanitise::inputInt($request->get('year'));
        $assignmentId   = InputSanitise::inputInt($request->get('assignment_id'));

        if( $isUpdate && isset($assignmentId)){
            $assignment = static::find($assignmentId);
            if(!is_object($assignment)){
            	return 'false';
            }
        } else {
            $assignment = new static;
        }
        $loginUser = Auth::user();
        $assignment->question = $question;
        $assignment->assignment_subject_id = $subjectId;
        $assignment->assignment_topic_id = $topicId;
        $assignment->lecturer_id = $loginUser->id;
        $assignment->college_id = $loginUser->college_id;
        $assignment->college_dept_id = $loginUser->college_dept_id;
        $assignment->year = $year;

        if($request->exists('attached_link')){
	        $attachmentFolderPath = "assignmentStorage/topicId-".$topicId;
	        if(!is_dir($attachmentFolderPath)){
	        	mkdir($attachmentFolderPath, 0755);
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

        /**
     *  get subject
     */
    public function subject(){
        return $this->belongsTo(AssignmentSubject::class, 'assignment_subject_id');
    }

    /**
     *  get topic
     */
    public function topic(){
        return $this->belongsTo(AssignmentTopic::class, 'assignment_topic_id');
    }

    protected static function getStudentAssignments(){
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)
                ->where('college_dept_id', $loginUser->college_dept_id)
                ->where('year', $loginUser->year)
                ->paginate();
    }

    protected static function getAssignments(Request $request){
        $loginUser = Auth::user();
        $resultQuery = static::where('college_id', $loginUser->college_id);

        if(!empty($request->department)){
            $resultQuery->where('college_dept_id', $request->department);
        } else {
            $resultQuery->where('college_dept_id', $loginUser->college_dept_id);
        }
        if(!empty($request->year)){
            $resultQuery->where('year', $request->year);
        }else if(User::Student == $loginUser->user_type){
            $resultQuery->where('year', $loginUser->year);
        }

        if(User::Lecturer == $loginUser->user_type){
            $resultQuery->where('lecturer_id', $loginUser->id);
        } else if(!empty($request->lecturer_id)){
            $resultQuery->where('lecturer_id', $request->lecturer_id);
        }

        if(!empty($request->subject)){
            $resultQuery->where('assignment_subject_id', $request->subject);
        }
        if(!empty($request->topic)){
            $resultQuery->where('assignment_topic_id', $request->topic);
        }
        return $resultQuery->get();
    }

    protected static function getAssignmentByTopic($topic){
        if(User::Student == Auth::user()->user_type){
            return static::where('assignment_topic_id',$topic)->where('year', Auth::user()->year)->first();
        } else {
            return static::where('assignment_topic_id',$topic)->first();
        }
    }

    protected static function checkAssignmentIsExist(Request $request){
        $result = [];
        $assignment = static::where('assignment_subject_id',$request->subject)
            ->where('assignment_topic_id', $request->topic)
            ->where('year', $request->year)
            ->first();
        if(is_object($assignment)){
            $result['status'] = 'true';
            $result['id'] = $assignment->id;
        } else {
            $result['status'] = 'false';
        }
        return $result;
    }
}
