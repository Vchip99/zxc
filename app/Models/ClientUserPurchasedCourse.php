<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB, Session;
use App\Models\ClientOnlineCourse;

class ClientUserPurchasedCourse extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'course_id' ,'client_id', 'payment_id', 'price'];

    protected static function getClientUserCourses($clientId){
    	$userCourses = [];
    	$results = static::where('client_id', $clientId)->get();
    	if(is_object($results) && false == $results->isEmpty()){
    		foreach($results as $result){
    			$userCourses[$result->user_id][] = $result->course_id;
    		}
    	}
    	return $userCourses;
    }

    protected static function getUserPurchasedCourses($clientId, $userId){
        $userCourses = [];
        $results = static::where('client_id', $clientId)->where('user_id', $userId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $userCourses[] = $result->course_id;
            }
        }
        return $userCourses;
    }

    protected static function getClientUserPurchasedCourses($clientId, $userId){
        return static::join('client_user_payments', 'client_user_payments.payment_id', '=', 'client_user_purchased_courses.payment_id')
                ->where('client_user_purchased_courses.client_id', $clientId)
                ->where('client_user_purchased_courses.user_id', $userId)
                ->select('client_user_purchased_courses.*', 'client_user_payments.updated_at')->get();
    }

    protected static function isCoursePurchased($clientId, $userId, $courseId){
        $course = static::where('client_id', $clientId)->where('user_id', $userId)->where('course_id', $courseId)->first();
        if(is_object($course)){
            return 'true';
        }
        return 'false';
    }

    protected static function changeClientUserCourseStatus(Request $request){
    	$userCourse = static::where('client_id', $request->client_id)->where('user_id', $request->client_user_id)->where('course_id', $request->course_id)->first();
    	if(false == is_object($userCourse)){
    		$newUserCourse = new static;
    		$newUserCourse->user_id = $request->client_user_id;
    		$newUserCourse->course_id = $request->course_id;
    		$newUserCourse->client_id = $request->client_id;
    		$newUserCourse->save();
    		return 'true';
    	}elseif(true == is_object($userCourse)){
    		$userCourse->delete();
    		return 'true';
    	} else {
    		return 'false';
    	}
    }

    public function course(){
        return $this->belongsTo(ClientOnlineCourse::class, 'course_id');
    }
}
