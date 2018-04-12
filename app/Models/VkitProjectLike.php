<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class VkitProjectLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vkit_project_id', 'user_id'];

    protected static function getLikeVkitProject(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('vkit_project_id',$request->get('project_id'))->where('user_id' ,$loginUser->id)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['vkit_project_id' => $request->get('project_id'), 'user_id' => $loginUser->id]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikesByVkitProjectId($id){
    	$likesCount = [];
        $likes = static::where('vkit_project_id', $id)->get();

        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->vkit_project_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->vkit_project_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeStatus(Request $request){
        return static::where('vkit_project_id',$request->get('project_id'))->get();
    }

    protected static function deleteVkitProjectLikesByUserId($userId){
        $vkitProjectLikes = static::where('user_id', $userId)->get();
        if(is_object($vkitProjectLikes) && false == $vkitProjectLikes->isEmpty()){
            foreach($vkitProjectLikes as $vkitProjectLike){
                $vkitProjectLike->delete();
            }
        }
    }
}
