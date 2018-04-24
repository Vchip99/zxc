<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class CourseVideoLike extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_video_id', 'user_id'];

    protected static function getLikeVideo(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('course_video_id',$request->get('video_id'))->where('user_id' ,$loginUser->id)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['course_video_id' => $request->get('video_id'), 'user_id' => $loginUser->id]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikesByVideoId($id){
    	$likesCount = [];
        $likes = static::where('course_video_id', $id)->get();

        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->course_video_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->course_video_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeStatus(Request $request){
        return static::where('course_video_id',$request->get('video_id'))->get();
    }

    protected static function deleteCourseVideoLikesByUserId($userId){
        $courseVideoLikes = static::where('user_id', $userId)->get();
        if(is_object($courseVideoLikes) && false == $courseVideoLikes->isEmpty()){
            foreach($courseVideoLikes as $courseVideoLike){
                $courseVideoLike->delete();
            }
        }
    }
}
