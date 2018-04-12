<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class DiscussionPostLike extends Model
{
    public $timestamps = false;

    const IsLike = 1;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['discussion_post_id', 'user_id', 'is_like'];

    protected static function getLikes(){
    	$likesCount = [];
        $likes = static::where('is_like', self::IsLike)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->discussion_post_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->discussion_post_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikePost(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('discussion_post_id',$request->get('post_id'))->where('user_id' ,$loginUser->id)->where( 'is_like', self::IsLike)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['discussion_post_id' => $request->get('post_id'), 'user_id' => $loginUser->id, 'is_like' => self::IsLike]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus(Request $request){
        return static::where('discussion_post_id',$request->get('post_id'))->where( 'is_like', self::IsLike)->get();
    }
}
