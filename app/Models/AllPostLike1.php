<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class AllPostLike extends Model
{
	public $timestamps = false;

    const IsLike = 1;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['all_post_id', 'user_id', 'episode_id', 'project_id', 'is_like'];

    protected static function getLikePost(Request $request){
        if(is_object(Auth::user())){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('all_post_id',$request->get('post_id'))->where('user_id' ,Auth::user()->id)->where('episode_id', $request->get('episode_id'))->where('project_id', $request->get('project_id'))->where( 'is_like', self::IsLike)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['all_post_id' => $request->get('post_id'), 'user_id' => Auth::user()->id, 'episode_id' => $request->get('episode_id'), 'project_id' => $request->get('project_id'), 'is_like' => self::IsLike]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLiksByEpisodeId($id){
    	$likesCount = [];
        $likes = static::where('episode_id', $id)->where('is_like', self::IsLike)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->all_post_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->all_post_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLiksByProjectId($id){
        $likesCount = [];
        $likes = static::where('project_id', $id)->where('is_like', self::IsLike)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->all_post_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->all_post_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeStatus(Request $request){
        return static::where('all_post_id',$request->get('post_id'))->where('episode_id', $request->get('episode_id'))->where('project_id', $request->get('project_id'))->where( 'is_like', self::IsLike)->get();
    }

    public function commentLikes(){
        return $this->hasMany(AllCommentLike::class, 'all_post_id');
    }
}
