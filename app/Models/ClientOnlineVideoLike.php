<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use Auth;

class ClientOnlineVideoLike extends Model
{
	protected $connection = 'mysql2';

    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_online_video_id', 'user_id', 'client_id'];

    protected static function getLikeVideo(Request $request){
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('client_online_video_id',$request->get('video_id'))->where('client_id', $loginUser->client_id)->where('user_id' ,$loginUser->id)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['client_online_video_id' => $request->get('video_id'), 'client_id' => $loginUser->client_id, 'user_id' => $loginUser->id]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikesByVideoId($id, Request $request){
    	$likesCount = [];
    	$client = InputSanitise::getCurrentClient($request);
        $likes = static::join('clients', 'clients.id', '=', 'client_online_video_likes.client_id')
                ->where('clients.subdomain', $client)
                ->where('client_online_video_likes.client_online_video_id', $id)
                ->select('client_online_video_likes.*')->get();

        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->client_online_video_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->client_online_video_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeStatus(Request $request){
        $loginUser = Auth::guard('clientuser')->user();
        return static::where('client_online_video_id',$request->get('video_id'))->where('client_id', $loginUser->client_id)->where('user_id' ,$loginUser->id)->get();
    }

    protected static function deleteClientOnlineVideoLikesByClientId($clientId){
        $videoLikes = static::where('client_id', $clientId)->get();
        if(is_object($videoLikes) && false == $videoLikes->isEmpty()){
            foreach($videoLikes as $videoLike){
                $videoLike->delete();
            }
        }
    }
}
