<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class CourseCommentLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_video_id', 'course_comment_id', 'user_id'];

    protected static function getLikeVideoComment(Request $request){
        if(is_object(Auth::user())){
            if( 1 == $request->get('dis_like')){
                $likeBlogComment = static::where('course_video_id',$request->get('video_id'))->where('user_id' ,Auth::user()->id)->where('course_comment_id', $request->get('comment_id'))->first();
                if(is_object($likeBlogComment)){
                    $likeBlogComment->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['course_video_id' => $request->get('video_id'), 'user_id' => Auth::user()->id, 'course_comment_id' => $request->get('comment_id')]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus($request){
    	return static::where('course_video_id',$request->get('video_id'))->where('course_comment_id', $request->get('comment_id'))->get();
    }

     protected static function getLikesByVideoId($id){
    	$commentLikesCount = [];

    	if($id > 0){
	        $likes = static::where('course_video_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->course_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->course_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }
}
