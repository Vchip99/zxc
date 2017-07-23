<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class RegisterOnlineCourse extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'online_course_id', 'grade'];

    protected static function registerCourse(Request $request){
    	$userId = $request->get('user_id');
    	$courseId = $request->get('course_id');
    	if(isset($userId) && isset($courseId)){
    		$registeredCourse = static::firstOrNew(['user_id' => $userId, 'online_course_id' => $courseId]);
    		if(is_object($registeredCourse) && empty($registeredCourse->id)){
    			$registeredCourse->save();
                return 'true';
    		} else {
                $registeredCourse->delete();
                return 'false';
            }
    	}
    }

    protected static function getRegisteredOnlineCoursesByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function isCourseRegistered($courseId){
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $registeredCourses = static::where('user_id', $userId)->where('online_course_id', $courseId)->get();
            if(false == $registeredCourses->isEmpty()){
                return 'true';
            }
        }
        return 'false';
    }

    protected static function deleteRegisteredOnlineCoursesByUserId($userId){
        $courses = static::where('user_id', $userId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $course->delete();
            }
        }
        return;
    }
}
