<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\ClientAllComment;
use App\Libraries\InputSanitise;
use Auth;

class ClientAllPost extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','client_id','all_post_module_id', 'episode_id', 'project_id', 'title', 'body'];

    /**
     *  post associated comments
     */
    public function comments()
    {
        return $this->hasMany(ClientAllComment::class)->where('parent_id', 0);
    }

    /**
     *  create post with respective module
     */
    protected static function createPost(Request $request){
    	$title = strip_tags(trim($request->get('title')));
    	$question = strip_tags(trim($request->get('question')));
    	$postModuleId = strip_tags(trim($request->get('all_post_module_id')));
        $episodeId = strip_tags(trim($request->get('episode_id')));
        $projectId = strip_tags(trim($request->get('project_id')));

    	$post = new static;
    	$post->user_id = Auth::guard('clientuser')->user()->id;
    	$post->client_id = Auth::guard('clientuser')->user()->client_id;
    	$post->title = $title;
    	$post->all_post_module_id = $postModuleId;
        $post->episode_id = $episodeId;
        $post->project_id = $projectId;
    	$post->body  = $question;
    	$post->save();
    	return $post;
    }

    protected static function getAllPostsByClient(Request $request, $allPostModule, $id){
        $client = InputSanitise::getCurrentClient($request);
        if($id > 2){
            return  static::join('clients', 'clients.id', '=', 'client_all_posts.client_id')
                ->where('clients.subdomain', $client)
                ->where('all_post_module_id',$allPostModule )
                ->where('episode_id', $id)
                ->select('client_all_posts.*')
                ->orderBy('client_all_posts.id', 'desc')->get();
        } else {
            return  static::where('all_post_module_id',$allPostModule )
                ->where('episode_id', $id)
                ->select('client_all_posts.*')
                ->orderBy('client_all_posts.id', 'desc')->get();
        }
    }

    public function deleteComments()
    {
        return $this->hasMany(ClientAllComment::class, 'client_all_post_id')->orderBy('id','desc');
    }
}