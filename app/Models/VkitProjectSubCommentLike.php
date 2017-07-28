<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class VkitProjectSubCommentLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vkit_project_id', 'vkit_project_comment_id', 'vkit_project_sub_comment_id', 'user_id'];

     protected static function getLikesByVkitProjectId($id){
    	$commentLikesCount = [];
    	if($id > 0){
	        $likes = static::where('vkit_project_id', $id)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->vkit_project_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->vkit_project_sub_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function getLikeVkitProject(Request $request){
    	if(is_object(Auth::user())){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'vkit_project_id' => $request->get('project_id'),
	    			'vkit_project_comment_id' => $request->get('comment_id'),
	    			'vkit_project_sub_comment_id' => $request->get('sub_comment_id'),
	    			'user_id' => Auth::user()->id
				]);
	    		return self::getSubCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('vkit_project_id', $request->get('project_id'))
	    					->where('vkit_project_comment_id', $request->get('comment_id'))
	    					->where('vkit_project_sub_comment_id', $request->get('sub_comment_id'))
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
    	return static::where('vkit_project_id', $request->get('project_id'))
    					->where('vkit_project_comment_id', $request->get('comment_id'))
    					->where('vkit_project_sub_comment_id', $request->get('sub_comment_id'))
    					->get();
    }

    protected static function deleteVkitProjectSubCommentLikesByUserId($userId){
        $subcommentLikes = static::where('user_id', $userId)->get();
        if(is_object($subcommentLikes) && false == $subcommentLikes->isEmpty()){
            foreach($subcommentLikes as $subcommentLike){
                $subcommentLike->delete();
            }
        }
    }
}
