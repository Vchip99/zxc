<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientInstituteCourse extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id'];

    protected static function addOrUpdateInstituteCourse($request, $isUpdate=false){
    	$courseName = InputSanitise::inputString($request->get('course'));
    	$courseId = InputSanitise::inputInt($request->get('course_id'));

    	if($courseId > 0 && $isUpdate){
    		$course = static::find($courseId);
    		if(!is_object($course)){
    			return Redirect::to('manageInstituteCourses');
    		}
    	} else {
    		$course = new static;
    	}

    	$course->name = $courseName;
    	$course->client_id = Auth::guard('client')->user()->id;
    	$course->save();
    	return $course;
    }
}
