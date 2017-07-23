<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\AllPost;
use App\Models\AllSubComment;
use App\Models\User;

class AllComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['all_post_id', 'user_id', 'parent_id', 'body'];

    /**
     *  create comment with post module
     */
    protected static function createComment(Request $request){
    	$postId = $request->get('all_post_id');
    	$userComment = $request->get('comment');

    	$comment = new AllComment;
    	$comment->body = $userComment;
    	$comment->all_post_id = $postId;
    	$comment->parent_id = 0;
    	$comment->user_id = \Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    /**
     *  create child comment with post module
     */
	protected static function createChildComment(Request $request){
    	$postId = $request->get('all_post_id');
    	$userComment = $request->get('comment');
    	$commentId = $request->get('comment_id');

    	$comment = new AllComment;
    	$comment->body = $userComment;
    	$comment->all_post_id = $postId;
    	$comment->parent_id = $commentId;
    	$comment->user_id = \Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    /**
     *  all posts
     */
    public function post()
    {
        return $this->belongsTo(AllPost::class);
    }

    /**
     *  post associated children
     */
    public function children()
    {
        // return $this->hasMany(AllComment::class, 'parent_id');
        return $this->hasMany(AllSubComment::class)->orderBy('id','desc');
    }

    /**
     *  post assocaited user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
