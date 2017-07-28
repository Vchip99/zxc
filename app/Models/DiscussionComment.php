<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DiscussionPost;
use App\Models\DiscussionSubComment;
use App\Models\User;
use App\Models\DiscussionCommentLike;
use Auth;

class DiscussionComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['discussion_post_id', 'user_id', 'body'];

    /**
     *  create discussion comment
     */
    protected static function createComment(Request $request){
        $postId = $request->get('discussion_post_id');
        $userComment = $request->get('comment');
        $commentId = $request->get('comment_id');

        $comment = new static;
        $comment->body = $userComment;
        $comment->discussion_post_id = $postId;
        $comment->user_id = Auth::user()->id;
        $comment->save();
        return $comment;
    }

    /**
     *  create discussion child comment
     */
	protected static function createChildComment(Request $request){
        return self::createComment($request);
    }


    /**
     *  post of discussion comment
     */
    public function post()
    {
        return $this->belongsTo(DiscussionPost::class);
    }

    /**
     *  children of discussion comment
     */
    public function children()
    {
        return $this->hasMany(DiscussionSubComment::class)->orderBy('id','desc');
    }

    /**
     *  user of discussion comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function commentLikes(){
        return $this->hasMany(DiscussionCommentLike::class, 'discussion_comment_id');
    }

}
