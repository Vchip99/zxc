<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class StudyMaterialPostLike extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['study_material_topic_id','study_material_post_id','user_id'];

    protected static function getLikes($topicId){
    	$likesCount = [];
        $likes = static::where('study_material_topic_id',$topicId)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->study_material_post_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->study_material_post_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikePost(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('study_material_topic_id',$request->get('topic_id'))->where('study_material_post_id',$request->get('post_id'))->where('user_id' ,$loginUser->id)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['study_material_topic_id' => $request->get('topic_id'),'study_material_post_id' => $request->get('post_id'), 'user_id' => $loginUser->id]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus(Request $request){
        return static::where('study_material_topic_id',$request->get('topic_id'))->where('study_material_post_id',$request->get('post_id'))->get();
    }

    protected static function deleteLikesByPostId($postId){
        $likes = static::where('study_material_post_id',$postId)->get();
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
