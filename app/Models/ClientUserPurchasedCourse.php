<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth,DB, Session,Cache;
use App\Models\ClientOnlineCourse;
use App\Models\Clientuser;
use App\Models\RegisterClientOnlineCourses;

class ClientUserPurchasedCourse extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'course_id' ,'client_id', 'payment_id', 'price', 'course'];

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

    protected static function getUserPurchasedCourseByClientIdById($clientId, $id){
        return static::join('client_user_payments', 'client_user_payments.payment_id', '=', 'client_user_purchased_courses.payment_id')
                ->where('client_user_purchased_courses.client_id', $clientId)
                ->where('client_user_purchased_courses.id', $id)
                ->select('client_user_purchased_courses.*', 'client_user_payments.updated_at')->first();
    }

    protected static function getClientUserPurchasedCourses($clientId, $userId){
        $result = static::join('client_user_payments', 'client_user_payments.payment_id', '=', 'client_user_purchased_courses.payment_id')
                ->where('client_user_purchased_courses.client_id', $clientId);
        if($userId > 0){
            $result->where('client_user_purchased_courses.user_id', $userId);
        }
        return $result->select('client_user_purchased_courses.*', 'client_user_payments.updated_at')->get();
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
            $newUserCourse->course = $newUserCourse->course->name;
    		$newUserCourse->save();
    		return 'true';
    	}elseif(true == is_object($userCourse)){
            RegisterClientOnlineCourses::deleteRegisteredOnlineCoursesByClientIdByUserIdByCourseId($userCourse->user_id,$userCourse->client_id,$userCourse->course_id);
    		$userCourse->delete();
    		return 'true';
    	} else {
    		return 'false';
    	}
    }

    public function course(){
        return $this->belongsTo(ClientOnlineCourse::class, 'course_id');
    }

    public function clientUser(){
        $user = Clientuser::find($this->user_id);
        if(is_object($user)){
            return $user->name;
        } else {
            return 'deleted';
        }
    }

    protected static function deleteClientUserCourses($clientId){
        $userCourses = [];
        $results = static::where('client_id', $clientId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }
}
