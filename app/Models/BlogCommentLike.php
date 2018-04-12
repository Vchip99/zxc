<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class BlogCommentLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['blog_id', 'blog_comment_id', 'user_id'];

     protected static function getLikeBlogComment(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likeBlogComment = static::where('blog_id',$request->get('blog_id'))->where('user_id' ,$loginUser->id)->where('blog_comment_id', $request->get('comment_id'))->first();
                if(is_object($likeBlogComment)){
                    $likeBlogComment->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['blog_id' => $request->get('blog_id'), 'user_id' => $loginUser->id, 'blog_comment_id' => $request->get('comment_id')]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus($request){
    	return static::where('blog_id',$request->get('blog_id'))->where('blog_comment_id', $request->get('comment_id'))->get();
    }

     protected static function getLikesByBlogId($id){
    	$commentLikesCount = [];

    	if($id > 0){
	        $likes = static::where('blog_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->blog_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->blog_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function deleteBlogCommentLikesByUserId($userId){
        $commentLikes = static::where('user_id', $userId)->get();
        if(is_object($commentLikes) && false == $commentLikes->isEmpty()){
            foreach($commentLikes as $commentLike){
                $commentLike->delete();
            }
        }
    }
}
