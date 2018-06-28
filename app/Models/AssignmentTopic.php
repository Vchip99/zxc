<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\AssignmentSubject;

class AssignmentTopic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','assignment_subject_id','lecturer_id', 'college_id', 'college_dept_id'];

    /**
     *  add/update assignment topic
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
        } else {
            $topic = new static;
        }
        $loginUser = Auth::user();
        $topic->name = $topicName;
        $topic->assignment_subject_id = $subjectId;
        $topic->lecturer_id = $loginUser->id;
        $topic->college_id = $loginUser->college_id;
        $topic->college_dept_id = $loginUser->college_dept_id;
        $topic->save();
        return $topic;
    }

    /**
     *  get subject of topic
     */
    public function subject(){
        return $this->belongsTo(AssignmentSubject::class, 'assignment_subject_id');
    }

    protected static function getAssignmentTopics($subjectId){
        $subjectId = InputSanitise::inputInt($subjectId);
        return static::join('assignment_subjects','assignment_subjects.id', '=', 'assignment_topics.assignment_subject_id')
                ->where('assignment_topics.assignment_subject_id', $subjectId)->select('assignment_topics.id', 'assignment_topics.*')->groupBy('assignment_topics.id')->get();
    }
}
