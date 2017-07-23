<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class LiveCourseSubCommentLike extends Model
{
   public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['live_course_video_id', 'live_course_comment_id', 'live_course_sub_comment_id', 'user_id'];

     protected static function getLikesByVideoId($id){
    	$commentLikesCount = [];
    	if($id > 0){
	        $likes = static::where('live_course_video_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->live_course_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->live_course_sub_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function getLikeVideoSubComment(Request $request){
    	if(is_object(Auth::user())){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'live_course_video_id' => $request->get('video_id'),
	    			'live_course_comment_id' => $request->get('comment_id'),
	    			'live_course_sub_comment_id' => $request->get('sub_comment_id'),
	    			'user_id' => Auth::user()->id
				]);
	    		return self::getSubCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('live_course_video_id', $request->get('video_id'))
	    					->where('live_course_comment_id', $request->get('comment_id'))
	    					->where('live_course_sub_comment_id', $request->get('sub_comment_id'))
	    					->where('user_id', Auth::user()->id)->first();
	    		if(is_object($commentLike)){
	    			$commentLike->delete();
	    			return self::getSubCommentStatus($request);
	    		}
	    	}
	    }
	    return 'false';
    }

    protected static function getSubCommentStatus(Request $request){
    	return static::where('live_course_video_id', $request->get('video_id'))
    					->where('live_course_comment_id', $request->get('comment_id'))
    					->where('live_course_sub_comment_id', $request->get('sub_comment_id'))
    					->get();
    }
}
