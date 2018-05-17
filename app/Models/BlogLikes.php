<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class BlogLikes extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['blog_id', 'user_id'];

    protected static function getLikeBlog(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('blog_id',$request->get('blog_id'))->where('user_id' ,$loginUser->id)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['blog_id' => $request->get('blog_id'), 'user_id' => $loginUser->id]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikesByBlogId($id){
    	$likesCount = [];
        $likes = static::where('blog_id', $id)->get();

        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->blog_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->blog_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeStatus(Request $request){
        return static::where('blog_id',$request->get('blog_id'))->get();
    }
}
