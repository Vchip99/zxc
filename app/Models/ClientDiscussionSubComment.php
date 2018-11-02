<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB,Cache,Auth;
use App\Models\ClientDiscussionLike;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\ClientDiscussionComment;


class ClientDiscussionSubComment extends Model
{
    protected $connection = 'mysql2';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_discussion_post_id', 'client_discussion_comment_id', 'clientuser_id', 'parent_id', 'body','client_id'];

    /**
     *  create discussion comment
     */
    protected static function createSubComment(Request $request){
        $postId = $request->get('discussion_post_id');
        $userComment = $request->get('subcomment');
        $commentId = $request->get('comment_id');
        $parentId = $request->get('parent_id');
        $loginUser = Auth::user();
        $parentSubComment = '';

        $comment = new static;
        if($parentId > 0){
        	$parentSubComment = static::find($parentId);
        }

        if(is_object($parentSubComment)){
        	$comment->body = $userComment;
        	if(Auth::guard('client')->user()){
        		$clientId = Auth::guard('client')->user()->id;
        		$userId = 0;
                $changedName = '<b>'.Auth::guard('client')->user()->name.'</b>';
        		$comment->body = str_replace(Auth::guard('client')->user()->name, $changedName, $userComment);
            } else {
            	$clientId = Auth::guard('clientuser')->user()->client_id;
        		$userId = Auth::guard('clientuser')->user()->id;
             	$changedName = '<b>'.Auth::guard('clientuser')->user()->name.'</b>';
        		$comment->body = str_replace(Auth::guard('clientuser')->user()->name, $changedName, $userComment);
            }
        } else {
        	$comment->body = $userComment;
        	if(Auth::guard('client')->user()){
	        	$clientId = Auth::guard('client')->user()->id;
	        	$userId = 0;
	        } else {
	        	$clientId = Auth::guard('clientuser')->user()->client_id;
	        	$userId = Auth::guard('clientuser')->user()->id;
	        }
        }

        $comment->client_discussion_post_id = $postId;
        $comment->client_discussion_comment_id = $commentId;
        $comment->parent_id = $parentId?:0;
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
}
