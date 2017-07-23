<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\ClientAllCommentLike;
use Auth;

class ClientAllPostLike extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';

    const IsLike = 1;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_all_post_id', 'user_id', 'client_id','episode_id', 'project_id', 'is_like'];

    protected static function getLikePost(Request $request){
        if(is_object(Auth::guard('clientuser')->user())){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('client_all_post_id',$request->get('post_id'))->where('user_id' ,Auth::guard('clientuser')->user()->id)->where('client_id',Auth::guard('clientuser')->user()->client_id)->where('episode_id', $request->get('episode_id'))->where('project_id', $request->get('project_id'))->where( 'is_like', self::IsLike)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['client_all_post_id' => $request->get('post_id'), 'user_id' => Auth::guard('clientuser')->user()->id, 'client_id' => Auth::guard('clientuser')->user()->client_id, 'episode_id' => $request->get('episode_id'), 'project_id' => $request->get('project_id'), 'is_like' => self::IsLike]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLiksByEpisodeId($id, Request $request){
    	$likesCount = [];
    	$client = InputSanitise::getCurrentClient($request);
        $likes = static::join('clients', 'clients.id', '=', 'client_all_post_likes.client_id')
        		->where('clients.subdomain', $client)
        		->where('episode_id', $id)->where('is_like', self::IsLike)->get();
        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->client_all_post_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->client_all_post_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

     protected static function getLikeStatus(Request $request){
     	$client = InputSanitise::getCurrentClient($request);
        return static::join('clients', 'clients.id', '=', 'client_all_post_likes.client_id')
        		->where('clients.subdomain', $client)
        		->where('client_all_post_id',$request->get('post_id'))
        		->where('episode_id', $request->get('episode_id'))
        		->where('project_id', $request->get('project_id'))
        		->where( 'is_like', self::IsLike)->get();
    }

    public function commentLikes(){
        return $this->hasMany(ClientAllCommentLike::class, 'client_all_post_id');
    }
}
