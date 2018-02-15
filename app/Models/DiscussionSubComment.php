<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DiscussionPost;
use App\Models\DiscussionComment;
use App\Models\User;
use App\Models\DiscussionSubCommentLike;
use Auth,Cache;

class DiscussionSubComment extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['discussion_post_id', 'discussion_comment_id', 'user_id', 'parent_id', 'body'];


    /**
     *  create discussion comment
     */
    protected static function createSubComment(Request $request){
        $postId = $request->get('discussion_post_id');
        $userComment = $request->get('subcomment');
        $commentId = $request->get('comment_id');
        $parentId = $request->get('parent_id');

        $comment = new static;

        $parentSubComment = DiscussionSubComment::find($parentId);

        if( is_object($parentSubComment) && $parentSubComment->user_id !== Auth::user()->id ){
        	$comment->body = $userComment;
        	$user = User::find($parentSubComment->user_id);
        	if(is_object($user)){
        		$changedName = '<b>'.$user->name.'</b>';
        		$comment->body = str_replace($user->name, $changedName, $userComment);
        	}
        } else {
        	$comment->body = $userComment;
        }

        $comment->discussion_post_id = $postId;
        $comment->discussion_comment_id = $commentId;
        $comment->parent_id = $parentId?:0;
        $comment->user_id = Auth::user()->id;
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
}
