<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientUserInstituteCourse extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_user_id', 'client_id', 'client_institute_course_id', 'test_permission', 'course_permission'];

    protected static function changeClientPermissionStatus(Request $request){
    	$isSuccess = 'false';
    	$clientUserId = InputSanitise::inputInt($request->get('client_user_id'));
    	$clientId = InputSanitise::inputInt($request->get('client_id'));
    	$clientInstituteCourseId = InputSanitise::inputInt($request->get('client_institute_course_id'));
    	$permissionType = InputSanitise::inputString($request->get('permission_type'));

    	$course = static::where('client_id', $clientId)->where('client_user_id', $clientUserId)->where('client_institute_course_id', $clientInstituteCourseId)->first();
    	if(is_object($course)){
    		DB::beginTransaction();
	        try
	        {
	    		if('test' == $permissionType){
	    			if(1 == $course->test_permission){
	    				$course->test_permission = 0;
	    			} else {
	    				$course->test_permission = 1;
	    			}
	    			$course->save();
	    			DB::commit();
	    			$isSuccess = 'true';
	    		} else if('course' == $permissionType){
	    			if(1 == $course->course_permission){
	    				$course->course_permission = 0;
	    			} else {
	    				$course->course_permission = 1;
	    			}
	    			$course->save();
	    			DB::commit();
					$isSuccess = 'true';
	    		} else {
	    			$isSuccess = 'false';
	    		}
	    	}
	        catch(\Exception $e)
	        {
	            DB::rollback();
	            return redirect()->back()->withErrors('something went wrong.');
	        }
    	}
    	return $isSuccess;
    }
}
