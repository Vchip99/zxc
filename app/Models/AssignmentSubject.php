<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class AssignmentSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','lecturer_id', 'college_id', 'college_dept_id', 'year'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateAssignmentSubject( Request $request, $isUpdate=false){
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $year   = InputSanitise::inputInt($request->get('year'));
        $subjectId   = InputSanitise::inputInt($request->get('subject_id'));
        if( $isUpdate && isset($subjectId)){
            $subject = static::find($subjectId);
            if(!is_object($subject)){
            	return Redirect::to('manageAssignmentSubject');
            }
        } else{
            $subject = new static;
        }
        $loginUser = Auth::user();
        $subject->name = $subjectName;
        $subject->lecturer_id = $loginUser->id;
        $subject->college_id = $loginUser->college_id;
        $subject->college_dept_id = $loginUser->college_dept_id;
        $subject->year = $year;
        $subject->save();
        return $subject;
    }

    protected static function getAssignmentSubjectsByYear($year,$lecturer=NULL,$collegeDept=NULL){
        $loginUser = Auth::user();
        if($lecturer > 0){
            $query = static::join('assignment_topics','assignment_topics.assignment_subject_id', '=', 'assignment_subjects.id')->where('assignment_subjects.lecturer_id', $lecturer);
        } else {
            $query = static::join('assignment_topics','assignment_topics.assignment_subject_id', '=', 'assignment_subjects.id')->where('assignment_subjects.lecturer_id', $loginUser->id);
        }
        $query->where('assignment_subjects.college_id', $loginUser->college_id);
        if($collegeDept > 0){
            $query->where('assignment_subjects.college_dept_id', $collegeDept);
        } else {
            $query->where('assignment_subjects.college_dept_id', $loginUser->college_dept_id);
        }
        return    $query->where('assignment_subjects.year', $year)->select('assignment_subjects.id','assignment_subjects.*')->groupBy('assignment_subjects.id')->get();
    }

    protected static function getAssignmentSubjectsOfGivenAssignmentByLecturer(Request $request){
        return static::join('assignment_questions', 'assignment_questions.assignment_subject_id', '=', 'assignment_subjects.id')->where('assignment_questions.lecturer_id',$request->lecturer_id)
            ->select('assignment_subjects.id','assignment_subjects.*')->groupBy('assignment_subjects.id')->get();
    }
}
