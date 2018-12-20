<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth,Cache;
use App\Model\StudyMaterialPost;

class StudyMaterialComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['study_material_topic_id','study_material_post_id','user_id','body'];

    /**
     *  create discussion comment
     */
    protected static function createComment(Request $request){
        $topicId = $request->get('topic_id');
        $postId = $request->get('post_id');
        $userComment = $request->get('comment');
        $commentId = $request->get('comment_id');

        $comment = new static;
        $comment->body = $userComment;
        $comment->study_material_topic_id = $topicId;
        $comment->study_material_post_id = $postId;
        $comment->user_id = Auth::user()->id;
        $comment->save();
        return $comment;
    }

    /**
     *  post of comment
     */
    public function post()
    {
        return $this->belongsTo(StudyMaterialPost::class);
    }

    /**
     *  children of comment
     */
    public function children()
    {
        return $this->hasMany(StudyMaterialSubComment::class, 'study_material_comment_id');
    }

    /**
     *  user of StudyMaterial comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    public function commentLikes(){
        return $this->hasMany(StudyMaterialCommentLike::class, 'study_material_comment_id');
    }

    protected static function deleteCommentsByPostId($postId){
        $comments = static::where('study_material_post_id',$postId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }

    protected static function deleteCommentsByTopicId($topicId){
        $comments = static::where('study_material_topic_id',$topicId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }
}
