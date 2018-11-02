<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB,Cache,Auth;
use App\Models\ClientDiscussionLike;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\ClientDiscussionComment;

class ClientDiscussionPost extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['clientuser_id', 'client_discussion_category_id', 'title', 'body','answer1','answer2','answer3','answer4','answer','solution','client_id'];

    /**
     *  create post
     */
    protected static function createPost(Request $request){
    	$title = strip_tags(trim($request->get('title')));
    	$question = $request->get('question');
    	$postCategoryId = strip_tags(trim($request->get('post_category_id')));

        $answer1 = $request->get('answer1');
        $answer2 = $request->get('answer2');
        $answer3 = $request->get('answer3');
        $answer4 = $request->get('answer4');
        $answer = $request->get('answer');
        $solution = $request->get('solution');

        if(Auth::guard('client')->user()){
        	$clientId = Auth::guard('client')->user()->id;
        	$userId = 0;
        } else {
        	$clientId = Auth::guard('clientuser')->user()->client_id;
        	$userId = Auth::guard('clientuser')->user()->id;
        }

    	$post = new static;
    	$post->clientuser_id = $userId;
    	$post->title = $title;
    	$post->client_discussion_category_id = $postCategoryId;
    	$post->body  = $question;
        $post->answer1  = $answer1;
        $post->answer2  = $answer2;
        $post->answer3  = $answer3;
        $post->answer4  = $answer4;
        $post->answer  = $answer;
        $post->solution  = $solution;
        $post->client_id  = $clientId;
    	$post->save();
    	return $post;
    }

    protected static function getPostsByClient(){
        if(Auth::guard('client')->user()){
            $clientId = Auth::guard('client')->user()->id;
            $userId = 0;
        } else {
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $userId = Auth::guard('clientuser')->user()->id;
        }
    	return static::where('client_id',$clientId)->orderBy('id','desc')->get();
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
     *  comments of discussion post
     */
    public function descComments()
    {
        return $this->hasMany(ClientDiscussionComment::class, 'client_discussion_post_id')->orderBy('id','desc');
    }

    public function comments(){
        return $this->hasMany(ClientDiscussionComment::class, 'client_discussion_post_id');
    }

    public function deleteCommantsAndSubComments(){
        if(is_object($this->comments) && false == $this->comments->isEmpty()){
            foreach($this->comments as $comment){
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subcomment){
                        // if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                        //     foreach($subcomment->deleteLikes as $subcommentLike){
                        //         $subcommentLike->delete();
                        //     }
                        // }
                        $subcomment->delete();
                    }
                }

                // if(is_object($comment->commentLikes) && false == $comment->commentLikes->isEmpty()){
                //     foreach($comment->commentLikes as $commentLike){
                //         $commentLike->delete();
                //     }
                // }
                $comment->delete();
            }
        }
        // if(is_object($this->deleteLikes) && false == $this->deleteLikes->isEmpty()){
        //     foreach($this->deleteLikes as $postLike){
        //         $postLike->delete();
        //     }
        // }
    }
}
