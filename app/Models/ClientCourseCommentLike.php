<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Clientuser;
use App\Models\ClientCourseSubComment;
use App\Libraries\InputSanitise;
use App\Models\CourseCommentLike;
use Auth;

class ClientCourseCommentLike extends Model
{
    protected $connection = 'mysql2';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_online_video_id', 'client_course_comment_id', 'user_id', 'client_id'];

    protected static function getLikeVideoComment(Request $request){
        if(is_object(Auth::guard('clientuser')->user())){
            if( 1 == $request->get('dis_like')){
                $likeBlogComment = static::where('client_online_video_id',$request->get('video_id'))
                                        ->where('client_id', Auth::guard('clientuser')->user()->client_id)
                                        ->where('user_id' ,Auth::guard('clientuser')->user()->id)
                                        ->where('client_course_comment_id', $request->get('comment_id'))
                                        ->first();
                if(is_object($likeBlogComment)){
                    $likeBlogComment->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['client_online_video_id' => $request->get('video_id'), 'client_id' => Auth::guard('clientuser')->user()->client_id, 'user_id' => Auth::guard('clientuser')->user()->id, 'client_course_comment_id' => $request->get('comment_id')]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus($request){
        $client = InputSanitise::getCurrentClient($request);
    	return static::join('clients', 'clients.id', '=', 'client_course_comment_likes.client_id')
                        ->where('clients.subdomain', $client)
                        ->where('client_online_video_id',$request->get('video_id'))
                        ->where('client_course_comment_id', $request->get('comment_id'))->get();
    }

    protected static function getLikesByVideoId($id, Request $request){
    	$commentLikesCount = [];
    	$client = InputSanitise::getCurrentClient($request);
    	if($id > 0){
	        $likes = static::join('clients', 'clients.id', '=', 'client_course_comment_likes.client_id')
                ->where('clients.subdomain', $client)
                ->where('client_course_comment_likes.client_online_video_id', $id)
                ->select('client_course_comment_likes.*')->get();

	        if( false == $likes->isEmpty() ){
	            foreach($likes as $like){
	                $commentLikesCount[$like->client_course_comment_id]['user_id'][$like->user_id] = $like->user_id;
	                $commentLikesCount[$like->client_course_comment_id]['like_id'][$like->id] = $like->id;
	            }
	        }
	    }
        return $commentLikesCount;
    }

    protected static function deleteClientCourseCommentLikesByClientId($clientId){
        $commentLikes = static::where('client_id', $clientId)->get();
        if(is_object($commentLikes) && false == $commentLikes->isEmpty()){
            foreach($commentLikes as $commentLike){
                $commentLike->delete();
            }
        }
    }
}
