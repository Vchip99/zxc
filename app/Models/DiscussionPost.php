<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DiscussionComment;
use DB, Cache;
use App\Models\DiscussionCommentLike;
use App\Models\DiscussionPostLike;
use App\Models\User;

class DiscussionPost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'category_id', 'title', 'body','answer1','answer2','answer3','answer4','answer','solution'];

    /**
     *  comments of discussion post
     */
    // public function comments()
    // {
    //     return $this->hasMany(DiscussionComment::class);
    // }

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

    	$post = new static;
    	$post->user_id = \Auth::user()->id;
    	$post->title = $title;
    	$post->category_id = $postCategoryId;
    	$post->body  = $question;
        $post->answer1  = $answer1;
        $post->answer2  = $answer2;
        $post->answer3  = $answer3;
        $post->answer4  = $answer4;
        $post->answer  = $answer;
        $post->solution  = $solution;
    	$post->save();
    	return $post;
    }

    /**
     *  return discussion post by filter array
     */
    protected static function getDuscussionPostsBySearchArray($request){
        $searchFilter = json_decode($request->get('arr'),true);
        $recent = $searchFilter['recent'];
        $mostpopular = $searchFilter['mostpopular'];

        $results = DiscussionPost::query();
        if( 1 == $recent ){
            $currentDate = date('Y-m-d H:i:s');
            $previousDate = date('Y-m-d H:i:s', strtotime("-30 days"));
            $results->whereBetween('created_at',[$previousDate, $currentDate]);
        }
        if( 1 == $mostpopular ){
            $arrIds = [];
            $arrDiscussion = DB::table('discussion_comments')->select('discussion_post_id')->groupBy('discussion_post_id')->get()->toArray();
            if(is_array($arrDiscussion)){
                foreach ($arrDiscussion as $discussion) {
                    $arrIds[] = $discussion->discussion_post_id;
                }
            }
            if(is_array($arrIds)){
                $results->whereIn('id',$arrIds);
            }
        }
        return $results->orderBy('id','desc')->get();
    }

    /**
     *  comments of discussion post
     */
    public function descComments()
    {
        return $this->hasMany(DiscussionComment::class, 'discussion_post_id')->orderBy('id','desc');
    }

    public function comments(){
        return $this->hasMany(DiscussionComment::class, 'discussion_post_id');
    }

    public function deleteLikes(){
        return $this->hasMany(DiscussionPostLike::class, 'discussion_post_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    public function deleteCommantsAndSubComments(){
        if(is_object($this->comments) && false == $this->comments->isEmpty()){
            foreach($this->comments as $comment){
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subcomment){
                        if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                            foreach($subcomment->deleteLikes as $subcommentLike){
                                $subcommentLike->delete();
                            }
                        }
                        $subcomment->delete();
                    }
                }

                if(is_object($comment->commentLikes) && false == $comment->commentLikes->isEmpty()){
                    foreach($comment->commentLikes as $commentLike){
                        $commentLike->delete();
                    }
                }
                $comment->delete();
            }
        }
        if(is_object($this->deleteLikes) && false == $this->deleteLikes->isEmpty()){
            foreach($this->deleteLikes as $postLike){
                $postLike->delete();
            }
        }
    }

    protected static function deleteAllDiscussionPostsByUserId($userId){
        $posts = static::where('user_id', $userId)->get();
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach($posts as $post){
                $post->deleteCommantsAndSubComments();
                $post->delete();
            }
        }
    }
}
