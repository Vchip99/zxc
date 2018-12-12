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
    protected $fillable = ['user_id', 'online_course_id', 'grade','payment_id','payment_request_id','price'];

    protected static function registerCourse(Request $request){
    	$userId = $request->get('user_id');
    	$courseId = $request->get('course_id');
    	if(isset($userId) && isset($courseId)){
    		$registeredCourse = static::firstOrNew(['user_id' => $userId, 'online_course_id' => $courseId]);
    		if(is_object($registeredCourse) && empty($registeredCourse->id)){
    			$registeredCourse->save();
                return 'true';
    		} else {
                if(empty($registeredCourse->price) && empty($registeredCourse->payment_id) && empty($registeredCourse->payment_request_id)){
                    $registeredCourse->delete();
                    return 'false';
                }
                return;
            }
    	}
    }

    protected static function getRegisteredOnlineCoursesByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function isCourseRegistered($courseId){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $userId = $loginUser->id;
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

    protected static function addPurchasedCourse($paymentArray){
        $purchasedCourse = new static;
        $purchasedCourse->user_id = $paymentArray['user_id'];
        $purchasedCourse->online_course_id = $paymentArray['online_course_id'];
        $purchasedCourse->payment_id = $paymentArray['payment_id'];
        $purchasedCourse->payment_request_id = $paymentArray['payment_request_id'];
        $purchasedCourse->price = $paymentArray['price'];
        $purchasedCourse->save();
        return $purchasedCourse;
    }

    protected static function getRegisteredOnlineCoursesByUserIdForPayments($userId){
        return static::join('course_courses','course_courses.id','=','register_online_courses.online_course_id')
            ->whereNotNull('register_online_courses.payment_id')
            ->whereNotNull('register_online_courses.payment_request_id')
            ->where('register_online_courses.price', '>', 0)
            ->whereNotNull('register_online_courses.payment_id')
            ->whereNotNull('register_online_courses.payment_request_id')
            ->where('register_online_courses.user_id', $userId)
            ->select('register_online_courses.id','register_online_courses.updated_at','register_online_courses.price','course_courses.name')
            ->groupBy('register_online_courses.id')->get();
    }
}
