<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\ClientAllPost;
use App\Models\Clientuser;
use App\Models\ClientAllSubComment;
use Auth;

class ClientAllComment extends Model
{
	protected $connection = 'mysql2';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_all_post_id', 'user_id', 'client_id', 'parent_id', 'body'];

    /**
     *  create comment with post module
     */
    protected static function createComment(Request $request){
    	$postId = $request->get('client_all_post_id');
    	$userComment = $request->get('comment');

    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->client_all_post_id = $postId;
    	$comment->parent_id = 0;
    	$comment->user_id = Auth::guard('clientuser')->user()->id;
    	$comment->client_id = Auth::guard('clientuser')->user()->client_id;
    	$comment->save();
    	return $comment;
    }

    /**
     *  create child comment with post module
     */
	protected static function createChildComment(Request $request){
    	$postId = $request->get('client_all_post_id');
    	$userComment = $request->get('comment');
    	$commentId = $request->get('comment_id');

    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->client_all_post_id = $postId;
    	$comment->parent_id = $commentId;
    	$comment->user_id = Auth::guard('clientuser')->user()->id;
    	$comment->client_id = Auth::guard('clientuser')->user()->client_id;
    	$comment->save();
    	return $comment;
    }

    /**
     *  all posts
     */
    public function post()
    {
        return $this->belongsTo(ClientAllPost::class);
    }

    /**
     *  post associated children
     */
    public function children()
    {
        return $this->hasMany(ClientAllSubComment::class)->orderBy('id', 'desc');
    }

    /**
     *  post assocaited user
     */
    public function user()
    {
        return $this->belongsTo(Clientuser::class);
    }
}
