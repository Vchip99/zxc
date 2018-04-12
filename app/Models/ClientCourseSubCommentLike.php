<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Clientuser;
use App\Libraries\InputSanitise;
use Auth;

class ClientCourseSubCommentLike extends Model
{
    protected $connection = 'mysql2';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_online_video_id', 'client_course_comment_id', 'client_course_sub_comment_id', 'user_id', 'client_id'];

    protected static function getLikesByVideoId($id, Request $request){
    	$commentLikesCount = [];
    	$client = InputSanitise::getCurrentClient($request);
    	if($id > 0){
	        $likes = static::join('clients', 'clients.id', '=', 'client_course_sub_comment_likes.client_id')
                ->where('clients.subdomain', $client)
                ->where('client_course_sub_comment_likes.client_online_video_id', $id)
                ->select('client_course_sub_comment_likes.*')->get();

	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->client_course_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->client_course_sub_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function getLikeVideoSubComment(Request $request){
    	$loginUser = Auth::guard('clientuser')->user();
    	if(is_object($loginUser)){
	    	if( 0 == $request->get('dis_like')){
	    		static::create([
	    			'client_online_video_id' => $request->get('video_id'),
	    			'client_course_comment_id' => $request->get('comment_id'),
	    			'client_course_sub_comment_id' => $request->get('sub_comment_id'),
	    			'client_id' => $loginUser->client_id,
	    			'user_id' => $loginUser->id
				]);
	    		return self::getSubCommentStatus($request);
	    	} else {
	    		$commentLike  = static::where('client_online_video_id', $request->get('video_id'))
	    					->where('client_course_comment_id', $request->get('comment_id'))
	    					->where('client_course_sub_comment_id', $request->get('sub_comment_id'))
	    					->where('client_id', $loginUser->client_id)
	    					->where('user_id', $loginUser->id)->first();
	    		if(is_object($commentLike)){
	    			$commentLike->delete();
	    			return self::getSubCommentStatus($request);
	    		}
	    	}
	    }
	    return 'false';
    }

    protected static function getSubCommentStatus(Request $request){
    	$client = InputSanitise::getCurrentClient($request);
    	return static::join('clients', 'clients.id', '=', 'client_course_sub_comment_likes.client_id')
            		->where('clients.subdomain', $client)
            		->where('client_course_sub_comment_likes.client_online_video_id', $request->get('video_id'))
					->where('client_course_sub_comment_likes.client_course_comment_id', $request->get('comment_id'))
					->where('client_course_sub_comment_likes.client_course_sub_comment_id', $request->get('sub_comment_id'))
					->get();
    }

    protected static function deleteClientCourseSubCommentLikesByUserId($clientId){
        $subcommentLikes = static::where('client_id', $clientId)->get();
        if(is_object($subcommentLikes) && false == $subcommentLikes->isEmpty()){
            foreach($subcommentLikes as $subcommentLike){
                $subcommentLike->delete();
            }
        }
    }
}
