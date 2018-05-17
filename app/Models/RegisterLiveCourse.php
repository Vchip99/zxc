<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;


class RegisterLiveCourse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'live_course_id'];

    protected static function registerCourse(Request $request){
    	$userId = $request->get('user_id');
    	$liveCourseId = $request->get('live_course_id');
    	if(isset($userId) && isset($liveCourseId)){
    		$registeredLiveCourse = static::firstOrNew(['user_id' => $userId, 'live_course_id' => $liveCourseId]);
    		if(is_object($registeredLiveCourse) && empty($registeredLiveCourse->id)){
    			$registeredLiveCourse->save();
                return 'true';
    		} else {
                $registeredLiveCourse->delete();
                return 'false';
            }
    	}
    }

    protected static function getRegisteredLiveCourses($userId){
    	return DB::table('register_live_courses')
    			->join('live_courses', 'live_courses.id', '=', 'register_live_courses.live_course_id')
                ->join('users', 'users.id', '=', 'register_live_courses.user_id')
                ->where('users.id', $userId)
    			->select('live_courses.*')
    			->get();
    }

    protected static function getCategoryIdsByRegisteredLiveCourses($userId){
        return DB::table('register_live_courses')
                ->join('live_courses', 'live_courses.id', '=', 'register_live_courses.live_course_id')
                ->where('register_live_courses.user_id', $userId)
                ->select('live_courses.category_id')
                ->get();
    }

    protected static function getRegisteredLiveCoursesByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function getRegisteredLiveCourseByUserIdByCourseId($userId, $liveCourseId){
        return static::where('user_id', $userId)->where('live_course_id', $liveCourseId)->get();
    }

    protected static function deleteRegisteredLiveCourseByUserIdByCourseId($userId){
        $courses = static::where('user_id', $userId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $course->delete();
            }
        }
        return;
    }
}
