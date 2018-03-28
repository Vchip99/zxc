<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class RegisterClientOnlineCourses extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_user_id', 'client_online_course_id', 'client_id'];

    protected static function registerCourse(Request $request){
    	$userId = $request->get('user_id');
    	$courseId = $request->get('course_id');
    	if(isset($userId) && isset($courseId)){
    		$registeredCourse = static::firstOrNew(['client_user_id' => $userId, 'client_online_course_id' => $courseId, 'client_id' =>  Auth::guard('clientuser')->user()->client_id]);
    		if(is_object($registeredCourse) && empty($registeredCourse->id)){
    			$registeredCourse->save();
                return 'true';
    		} else {
                $registeredCourse->delete();
                return 'false';
            }
    	}
    }

    protected static function isCourseRegistered($courseId){
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            $registeredCourses = static::where('client_user_id', $loginUser->id)
                                ->where('client_id', $loginUser->client_id)
                                ->where('client_online_course_id', $courseId)
                                ->get();
            if(false == $registeredCourses->isEmpty()){
                return 'true';
            }
        }
        return 'false';
    }

    protected static function deleteRegisteredOnlineCoursesByUserId($userId,$clientId){
        $courses = static::where('client_user_id', $userId)->where('client_id', $clientId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $course->delete();
            }
        }
        return;
    }

    protected static function deleteRegisteredOnlineCoursesClientId($clientId){
        $courses = static::where('client_id', $clientId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $course->delete();
            }
        }
        return;
    }
}
