<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DiscussionComment;
use DB;

class DiscussionPost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'category_id', 'title', 'body'];

    /**
     *  comments of discussion post
     */
    public function comments()
    {
        return $this->hasMany(DiscussionComment::class);
    }

    /**
     *  create post
     */
    protected static function createPost(Request $request){
    	$title = strip_tags(trim($request->get('title')));
    	$question = $request->get('question');
    	$postCategoryId = strip_tags(trim($request->get('post_category_id')));

    	$post = new DiscussionPost;
    	$post->user_id = \Auth::user()->id;
    	$post->title = $title;
    	$post->category_id = $postCategoryId;
    	$post->body  = $question;
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
            $currentDate = date('Y-m-d h:i:s');
            $previousDate = date('Y-m-d h:i:s', strtotime("-30 days"));
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
        return $this->hasMany(DiscussionComment::class)->orderBy('id','desc');
    }
}
