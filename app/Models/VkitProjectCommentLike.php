<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class VkitProjectCommentLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vkit_project_id', 'vkit_project_comment_id', 'user_id'];

     protected static function getLikeVkitProject(Request $request){
        if(is_object(Auth::user())){
            if( 1 == $request->get('dis_like')){
                $likeBlogComment = static::where('vkit_project_id',$request->get('project_id'))->where('user_id' ,Auth::user()->id)->where('vkit_project_comment_id', $request->get('comment_id'))->first();
                if(is_object($likeBlogComment)){
                    $likeBlogComment->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['vkit_project_id' => $request->get('project_id'), 'user_id' => Auth::user()->id, 'vkit_project_comment_id' => $request->get('comment_id')]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus($request){
    	return static::where('vkit_project_id',$request->get('project_id'))->where('vkit_project_comment_id', $request->get('comment_id'))->get();
    }

     protected static function getLikesByVkitProjectId($id){
    	$commentLikesCount = [];

    	if($id > 0){
	        $likes = static::where('vkit_project_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->vkit_project_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->vkit_project_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }
}
