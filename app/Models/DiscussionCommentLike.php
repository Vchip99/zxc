<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;


class DiscussionCommentLike extends Model
{
     public $timestamps = false;

    const IsLike = 1;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['discussion_post_id','discussion_comment_id', 'user_id', 'is_like'];

    protected static function getLiksByPosts($posts){
    	$postIds = [];
    	$commentLikesCount = [];
    	if( false == $posts->isEmpty()){
            foreach($posts as $post){
                $postIds[] = $post->id;
            }
        }

    	if(count($postIds) > 0){
	        $likes = static::whereIn('discussion_post_id', $postIds)->where('is_like', self::IsLike)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->discussion_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->discussion_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function getLikeComment(Request $request){
    	$loginUser = Auth::user();
    	if(is_object($loginUser)){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'discussion_post_id' => $request->get('post_id'),
	    			'discussion_comment_id' => $request->get('comment_id'),
	    			'user_id' => $loginUser->id,
	    			'is_like' => self::IsLike
				]);
	    		return self::getCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('discussion_post_id', $request->get('post_id'))->where('discussion_comment_id', $request->get('comment_id'))->where('is_like', self::IsLike)->where('user_id', $loginUser->id)->first();
	    		if(is_object($commentLike)){
	    			$commentLike->delete();
	    			return self::getCommentStatus($request);
	    		}
	    	}
	    }
	    return 'false';
    }

    protected static function getCommentStatus(Request $request){
    	return static::where('discussion_post_id', $request->get('post_id'))->where('discussion_comment_id', $request->get('comment_id'))->where('is_like', self::IsLike)->get();
    }
}
