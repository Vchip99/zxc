<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB,Cache,Auth;
use App\Models\ClientDiscussionPost;
use App\Models\ClientDiscussionLike;
use App\Models\ClientDiscussionSubComment;
use App\Models\Clientuser;
use App\Models\Client;

class ClientDiscussionComment extends Model
{
    protected $connection = 'mysql2';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_discussion_post_id','clientuser_id','body','client_id'];

    /**
     *  create discussion comment
     */
    protected static function createComment(Request $request){
        $postId = $request->get('discussion_post_id');
        $userComment = $request->get('comment');
        $commentId = $request->get('comment_id');

        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }

        $comment = new static;
        $comment->body = $userComment;
        $comment->client_discussion_post_id = $postId;
        $comment->clientuser_id = $userId;
        $comment->client_id = $clientId;
        $comment->save();
        return $comment;
    }

    public function getUser($userId){
        return Cache::remember('client:user-'.$userId,30, function() use($userId){
            return Clientuser::find($userId);
        });
    }

    public function getClient($userId){
        return Cache::remember('client-'.$userId,30, function() use($userId){
            return Client::find($userId);
        });
    }

    /**
     *  children of discussion comment
     */
    public function children()
    {
        return $this->hasMany(ClientDiscussionSubComment::class);
    }
}
