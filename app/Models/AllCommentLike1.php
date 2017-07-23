<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;


class AllCommentLike extends Model
{
    public $timestamps = false;

    const IsLike = 1;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['all_module_id', 'all_episode_id', 'all_project_id', 'all_comment_id', 'user_id', 'is_like'];

    protected static function getLiksByProjectId($id){
    	$commentLikesCount = [];

        $likes = static::whereIn('all_episode_id', $id)->where('is_like', self::IsLike)->get();

        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $commentLikesCount[$like->all_comment_id]['user_id'][$like->user_id] = $like->user_id;
                $commentLikesCount[$like->all_comment_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $commentLikesCount;
    }

    protected static function getLikeComment(Request $request){
    	if(is_object(Auth::user())){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'all_post_id' => $request->get('post_id'),
	    			'all_comment_id' => $request->get('comment_id'),
	    			'user_id' => Auth::user()->id,
	    			'is_like' => self::IsLike
				]);
	    		return self::getCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('all_post_id', $request->get('post_id'))->where('all_comment_id', $request->get('comment_id'))->where('is_like', self::IsLike)->where('user_id', Auth::user()->id)->first();
	    		if(is_object($commentLike)){
	    			$commentLike->delete();
	    			return self::getCommentStatus($request);
	    		}
	    	}
	    }
	    return 'false';
    }

    protected static function getCommentStatus(Request $request){
    	return static::where('all_post_id', $request->get('post_id'))->where('all_comment_id', $request->get('comment_id'))->where('is_like', self::IsLike)->get();
    }
}
