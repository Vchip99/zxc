<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\AllComment;

class AllPost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'all_post_module_id', 'episode_id', 'project_id', 'title', 'body'];

    /**
     *  post associated comments
     */
    public function comments()
    {
        return $this->hasMany(AllComment::class)->where('parent_id', 0);
    }

    /**
     *  create post with respective module
     */
    protected static function createPost(Request $request){
    	$title = strip_tags(trim($request->get('title')));
    	$question = $request->get('question');
    	$postModuleId = strip_tags(trim($request->get('all_post_module_id')));
        $episodeId = strip_tags(trim($request->get('episode_id')));
        $projectId = strip_tags(trim($request->get('project_id')));

    	$post = new AllPost;
    	$post->user_id = \Auth::user()->id;
    	$post->title = $title;
    	$post->all_post_module_id = $postModuleId;
        $post->episode_id = $episodeId;
        $post->project_id = $projectId;
    	$post->body  = $question;
    	$post->save();
    	return $post;
    }

    public function deleteComments()
    {
        return $this->hasMany(AllComment::class, 'all_post_id')->orderBy('id','desc');
    }
}
