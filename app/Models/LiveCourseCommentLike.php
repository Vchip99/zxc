<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class LiveCourseCommentLike extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['live_course_video_id', 'live_course_comment_id', 'user_id'];

     protected static function getLikeVideoComment(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likeBlogComment = static::where('live_course_video_id',$request->get('video_id'))->where('user_id' ,$loginUser->id)->where('live_course_comment_id', $request->get('comment_id'))->first();
                if(is_object($likeBlogComment)){
                    $likeBlogComment->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['live_course_video_id' => $request->get('video_id'), 'user_id' => $loginUser->id, 'live_course_comment_id' => $request->get('comment_id')]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus($request){
    	return static::where('live_course_video_id',$request->get('video_id'))->where('live_course_comment_id', $request->get('comment_id'))->get();
    }

     protected static function getLikesByVideoId($id){
    	$commentLikesCount = [];

    	if($id > 0){
	        $likes = static::where('live_course_video_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->live_course_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->live_course_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function deleteLiveCourseCommentLikesByUserId($userId){
        $commentLikes = static::where('user_id', $userId)->get();
        if(is_object($commentLikes) && false == $commentLikes->isEmpty()){
            foreach($commentLikes as $commentLike){
                $commentLike->delete();
            }
        }
    }
}
