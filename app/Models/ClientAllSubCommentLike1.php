<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use Auth;

class ClientAllSubCommentLike extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    const IsLike = 1;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_all_post_id','client_all_comment_id','client_all_sub_comment_id', 'user_id', 'client_id', 'is_like'];

    protected static function getLiksByPosts($posts, Request $request){
    	$postIds = [];
    	$subcommentLikesCount = [];
    	$client = InputSanitise::getCurrentClient($request);
    	if( false == $posts->isEmpty()){
            foreach($posts as $post){
                $postIds[] = $post->id;
            }
        }

    	if(count($postIds) > 0){
	        $likes = static::join('clients', 'clients.id', '=', 'client_all_sub_comment_likes.client_id')
        		->where('clients.subdomain', $client)
        		->whereIn('client_all_post_id', $postIds)
        		->where('is_like', self::IsLike)->get();
	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $subcommentLikesCount[$like->client_all_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $subcommentLikesCount[$like->client_all_sub_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $subcommentLikesCount;
    }

     protected static function getLikeSubComment(Request $request){

    	if(is_object(Auth::guard('clientuser')->user())){

	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'client_all_post_id' => $request->get('post_id'),
	    			'client_all_comment_id' => $request->get('comment_id'),
	    			'client_all_sub_comment_id' => $request->get('sub_comment_id'),
	    			'user_id' => Auth::guard('clientuser')->user()->id,
	    			'client_id' => Auth::guard('clientuser')->user()->client_id,
	    			'is_like' => self::IsLike
				]);
	    		return self::getCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('client_all_post_id', $request->get('post_id'))->where('client_all_comment_id', $request->get('comment_id'))->where('client_all_sub_comment_id', $request->get('sub_comment_id'))->where('is_like', self::IsLike)->where('user_id', Auth::guard('clientuser')->user()->id)->where('client_id', Auth::guard('clientuser')->user()->client_id)->first();

	    		if(is_object($commentLike)){
	    			$commentLike->delete();
	    			return self::getCommentStatus($request);
	    		}
	    	}
	    }
	    return 'false';
    }

    protected static function getCommentStatus(Request $request){
    	return static::where('client_all_post_id', $request->get('post_id'))->where('client_all_comment_id', $request->get('comment_id'))->where('client_all_sub_comment_id', $request->get('sub_comment_id'))->where('is_like', self::IsLike)->where('client_id', Auth::guard('clientuser')->user()->client_id)->get();
    }
}
