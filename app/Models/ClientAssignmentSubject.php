<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientInstituteCourse;

class ClientAssignmentSubject extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'client_institute_course_id'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateAssignmentSubject( Request $request, $isUpdate=false){
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId   = InputSanitise::inputInt($request->get('subject_id'));
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));

        if( $isUpdate && isset($subjectId)){
            $subject = static::find($subjectId);
            if(!is_object($subject)){
            	return Redirect::to('manageAssignmentSubject');
            }
        } else{
            $subject = new static;
        }
        $subject->name = $subjectName;
        $subject->client_id = Auth::guard('client')->user()->id;
        $subject->client_institute_course_id = $instituteCourseId;
        $subject->save();
        return $subject;
    }

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }

    protected static function getAssignmentSubjectsByCourse($instituteCourseid){
        if(is_object(Auth::guard('client')->user())){
    	   return static::where('client_id', Auth::guard('client')->user()->id)->where('client_institute_course_id', $instituteCourseid)->get();
        } else {
            return static::where('client_id', Auth::guard('clientuser')->user()->client_id)->where('client_institute_course_id', $instituteCourseid)->get();
        }
    }
}
