<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class BlogSubCommentLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['blog_id', 'blog_comment_id', 'blog_sub_comment_id', 'user_id'];

     protected static function getLikesByBlogId($id){
    	$commentLikesCount = [];
    	if($id > 0){
	        $likes = static::where('blog_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->blog_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->blog_sub_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function getLikeBlogSubComment(Request $request){
    	if(is_object(Auth::user())){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'blog_id' => $request->get('blog_id'),
	    			'blog_comment_id' => $request->get('comment_id'),
	    			'blog_sub_comment_id' => $request->get('sub_comment_id'),
	    			'user_id' => Auth::user()->id
				]);
	    		return self::getSubCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('blog_id', $request->get('blog_id'))
	    					->where('blog_comment_id', $request->get('comment_id'))
	    					->where('blog_sub_comment_id', $request->get('sub_comment_id'))
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
    	return static::where('blog_id', $request->get('blog_id'))
    					->where('blog_comment_id', $request->get('comment_id'))
    					->where('blog_sub_comment_id', $request->get('sub_comment_id'))
    					->get();
    }
}
