<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class ClientDiscussionLike extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_discussion_post_id','client_discussion_comment_id', 'client_discussion_sub_comment_id', 'clientuser_id', 'client_id', 'created_by'];

    protected static function getPostLikes(){
    	$likesCount = [];
    	if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }
        $likes = static::where('client_discussion_comment_id',0)->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
            	if(0 == $like->clientuser_id){
                	$likesCount[$like->client_discussion_post_id]['user_id'][$like->client_id] = $like->client_id;
            	} else {
                	$likesCount[$like->client_discussion_post_id]['user_id'][$like->clientuser_id] = $like->clientuser_id;
            	}
                $likesCount[$like->client_discussion_post_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikePost(Request $request){
        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
            $likePost = static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',0)->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->where('clientuser_id' ,$userId)->where('created_by',1)->first();
            if(is_object($likePost)){
                $likePost->delete();
                return self::getPostLikeStatus($request);
            } else {
                static::create(['client_discussion_post_id' => $request->get('post_id'), 'client_discussion_comment_id' => 0, 'client_discussion_sub_comment_id' =>0, 'client_id' => $clientId, 'clientuser_id' => $userId, 'created_by' => 1]);
                return self::getPostLikeStatus($request);
            }
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
            $likePost = static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',0)->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->where('clientuser_id' ,$userId)->where('created_by',0)->first();
            if(is_object($likePost)){
                $likePost->delete();
                return self::getPostLikeStatus($request);
            } else {
                static::create(['client_discussion_post_id' => $request->get('post_id'), 'client_discussion_comment_id' => 0, 'client_discussion_sub_comment_id' =>0, 'client_id' => $clientId, 'clientuser_id' => $userId, 'clientuser_id' => $userId, 'created_by' => 1]);
                return self::getPostLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getPostLikeStatus(Request $request){
        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }
        return static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',0)->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->get();
    }

    protected static function getCommentLikes(){
    	$likesCount = [];
    	if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }
        $likes = static::where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
            	if(0 == $like->clientuser_id){
                	$likesCount[$like->client_discussion_comment_id]['user_id'][$like->client_id] = $like->client_id;
            	} else {
                	$likesCount[$like->client_discussion_comment_id]['user_id'][$like->clientuser_id] = $like->clientuser_id;
            	}
                $likesCount[$like->client_discussion_comment_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeComment(Request $request){
        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
            $likePost = static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',$request->get('comment_id'))->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->where('clientuser_id' ,$userId)->where('created_by',1)->first();
            if(is_object($likePost)){
                $likePost->delete();
                return self::getCommentLikeStatus($request);
            } else {
                static::create(['client_discussion_post_id' => $request->get('post_id'), 'client_discussion_comment_id' => $request->get('comment_id'), 'client_discussion_sub_comment_id' => 0, 'client_id' => $clientId, 'clientuser_id' => $userId,'created_by' => 1]);
                return self::getCommentLikeStatus($request);
            }
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
            $likePost = static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id', $request->get('comment_id'))->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->where('clientuser_id' ,$userId)->where('created_by',0)->first();
            if(is_object($likePost)){
                $likePost->delete();
                return self::getCommentLikeStatus($request);
            } else {
                static::create(['client_discussion_post_id' => $request->get('post_id'), 'client_discussion_comment_id' => $request->get('comment_id'), 'client_discussion_sub_comment_id' => 0, 'client_id' => $clientId, 'clientuser_id' => $userId,'created_by' => 0]);
                return self::getCommentLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getCommentLikeStatus(Request $request){
        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }
        return static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',$request->get('comment_id'))->where('client_discussion_sub_comment_id',0)->where('client_id' ,$clientId)->get();
    }


    protected static function getSubCommentLikes(){
    	$likesCount = [];
    	if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }
        $likes = static::where('client_discussion_post_id', '!=',0)->where('client_discussion_comment_id','!=',0)->where('client_discussion_sub_comment_id','!=',0)->where('client_id' ,$clientId)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
            	if(0 == $like->clientuser_id){
                	$likesCount[$like->client_discussion_sub_comment_id]['user_id'][$like->client_id] = $like->client_id;
            	} else {
                	$likesCount[$like->client_discussion_sub_comment_id]['user_id'][$like->clientuser_id] = $like->clientuser_id;
            	}
                $likesCount[$like->client_discussion_sub_comment_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeSubComment(Request $request){
        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
            $likePost = static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',$request->get('comment_id'))->where('client_discussion_sub_comment_id',$request->get('sub_comment_id'))->where('client_id' ,$clientId)->where('clientuser_id' ,$userId)->where('created_by',1)->first();
            if(is_object($likePost)){
                $likePost->delete();
                return self::getSubCommentLikeStatus($request);
            } else {
                static::create(['client_discussion_post_id' => $request->get('post_id'), 'client_discussion_comment_id' => $request->get('comment_id'), 'client_discussion_sub_comment_id' => $request->get('sub_comment_id'), 'client_id' => $clientId, 'clientuser_id' => $userId, 'created_by' => 1]);
                return self::getSubCommentLikeStatus($request);
            }
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
            $likePost = static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id', $request->get('comment_id'))->where('client_discussion_sub_comment_id',$request->get('sub_comment_id'))->where('client_id' ,$clientId)->where('clientuser_id' ,$userId)->where('created_by',0)->first();
            if(is_object($likePost)){
                $likePost->delete();
                return self::getSubCommentLikeStatus($request);
            } else {
                static::create(['client_discussion_post_id' => $request->get('post_id'), 'client_discussion_comment_id' => $request->get('comment_id'), 'client_discussion_sub_comment_id' => $request->get('sub_comment_id'), 'client_id' => $clientId, 'clientuser_id' => $userId, 'created_by' => 0]);
                return self::getSubCommentLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getSubCommentLikeStatus(Request $request){
        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }
        return static::where('client_discussion_post_id',$request->get('post_id'))->where('client_discussion_comment_id',$request->get('comment_id'))->where('client_discussion_sub_comment_id',$request->get('sub_comment_id'))->where('client_id' ,$clientId)->get();
    }
}
