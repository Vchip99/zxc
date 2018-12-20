<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth,Cache;

class StudyMaterialSubCommentLike extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   protected $fillable = ['study_material_topic_id','study_material_post_id','study_material_comment_id','study_material_sub_comment_id','user_id'];

    protected static function getLiksByPosts($posts){
    	$postIds = [];
    	$commentLikesCount = [];
    	if( false == $posts->isEmpty()){
            foreach($posts as $post){
                $postIds[] = $post->id;
            }
        }

    	if(count($postIds) > 0){
	        $likes = static::whereIn('study_material_post_id', $postIds)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->study_material_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->study_material_sub_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function getLikeSubComment(Request $request){
    	$loginUser = Auth::user();
    	if(is_object($loginUser)){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'study_material_topic_id' => $request->get('topic_id'),
	    			'study_material_post_id' => $request->get('post_id'),
	    			'study_material_comment_id' => $request->get('comment_id'),
	    			'study_material_sub_comment_id' => $request->get('sub_comment_id'),
	    			'user_id' => $loginUser->id
				]);
	    		return self::getSubCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('study_material_topic_id',$request->get('topic_id'))
	    					->where('study_material_post_id', $request->get('post_id'))
	    					->where('study_material_comment_id', $request->get('comment_id'))
	    					->where('study_material_sub_comment_id', $request->get('sub_comment_id'))
	    					->where('user_id', $loginUser->id)->first();
	    		if(is_object($commentLike)){
	    			$commentLike->delete();
	    			return self::getSubCommentStatus($request);
	    		}
	    	}
	    }
	    return 'false';
    }

    protected static function getSubCommentStatus(Request $request){
    	return static::where('study_material_topic_id',$request->get('topic_id'))
    					->where('study_material_post_id', $request->get('post_id'))
    					->where('study_material_comment_id', $request->get('comment_id'))
    					->where('study_material_sub_comment_id', $request->get('sub_comment_id'))
    					->get();
    }

    protected static function deleteLikesByPostId($postId){
        $likes = static::where('study_material_post_id',$postId)->get();
        if(is_object($likes) && false == $likes->isEmpty()){
            foreach($likes as $like){
                $like->delete();
            }
        }
    }

    protected static function deleteLikesBySubCommentId($subcommentId){
        $likes = static::where('study_material_sub_comment_id',$subcommentId)->get();
        if(is_object($likes) && false == $likes->isEmpty()){
            foreach($likes as $like){
                $like->delete();
            }
        }
    }

    protected static function deleteLikesByCommentId($commentId){
        $likes = static::where('study_material_comment_id',$commentId)->get();
        if(is_object($likes) && false == $likes->isEmpty()){
            foreach($likes as $like){
                $like->delete();
            }
        }
    }

    protected static function deleteLikesByTopicId($topicId){
        $likes = static::where('study_material_topic_id',$topicId)->get();
        if(is_object($likes) && false == $likes->isEmpty()){
            foreach($likes as $like){
                $like->delete();
            }
        }
    }
}
