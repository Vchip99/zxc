<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use App\Models\StudyMaterialTopic;
use App\Models\StudyMaterialComment;
use DB,Auth,Cache;

class StudyMaterialSubComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['study_material_topic_id','study_material_post_id','study_material_comment_id','user_id', 'parent_id','body'];


    /**
     *  create discussion comment
     */
    protected static function createSubComment(Request $request){
        $topicId = $request->get('topic_id');
        $postId = $request->get('post_id');
        $userComment = $request->get('subcomment');
        $commentId = $request->get('comment_id');
        $parentId = $request->get('parent_id');
        $loginUser = Auth::user();

        $comment = new static;
        $parentSubComment = static::find($parentId);

        if( is_object($parentSubComment) && $parentSubComment->user_id !== $loginUser->id ){
        	$comment->body = $userComment;
        	$user = User::find($parentSubComment->user_id);
        	if(is_object($user)){
        		$changedName = '<b>'.$user->name.'</b>';
        		$comment->body = str_replace($user->name, $changedName, $userComment);
        	}
        } else {
        	$comment->body = $userComment;
        }

        $comment->study_material_topic_id = $topicId;
        $comment->study_material_post_id = $postId;
        $comment->study_material_comment_id = $commentId;
        $comment->parent_id = $parentId?:0;
        $comment->user_id = $loginUser->id;
        $comment->save();
        return $comment;
    }

    public function deleteLikes(){
        return $this->hasMany(DiscussionSubCommentLike::class, 'discussion_sub_comment_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    protected static function deleteSubCommentsByPostId($postId){
        $comments = static::where('study_material_post_id',$postId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }

    protected static function deleteSubCommentByCommentId($commentId){
        $comments = static::where('study_material_comment_id',$commentId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }

    protected static function deleteSubCommentsByTopicId($topicId){
        $comments = static::where('study_material_topic_id',$topicId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }
}
