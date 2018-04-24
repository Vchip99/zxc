<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Clientuser;
use App\Models\ClientCourseSubComment;
use App\Libraries\InputSanitise;
use App\Models\ClientCourseCommentLike;
use Auth,Cache;

class ClientCourseComment extends Model
{
	protected $connection = 'mysql2';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_online_video_id', 'user_id', 'client_id', 'body'];

    /**
     *  create comment with assocaited  Id
     */
    protected static function createComment(Request $request){
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	$userComment = $request->get('comment');
        $loginUser = Auth::guard('clientuser')->user();
    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->client_online_video_id = $videoId;
    	$comment->user_id = $loginUser->id;
    	$comment->client_id = $loginUser->client_id;
    	$comment->save();
    	return $comment;
    }

    public function children(){
    	return $this->hasMany(ClientCourseSubComment::class);
    }

    public function deleteLikes(){
        return $this->hasMany(ClientCourseCommentLike::class);
    }

    public function user(){
        return $this->belongsTo(Clientuser::class, 'user_id');
    }

    public function getClientUser($subdomainName,$userId){
        return Clientuser::find($userId);
    }

    public static function getCommentsByVideoId($id, Request $request){
    	$client = InputSanitise::getCurrentClient($request);
        if($id > 0){
            return  static::join('clients', 'clients.id', '=', 'client_course_comments.client_id')
                ->where('clients.subdomain', $client)
                ->where('client_online_video_id', $id)
                ->select('client_course_comments.*')
                ->orderBy('client_course_comments.id', 'desc')->get();
        } else {
            return  static::where('client_online_video_id', $id)
                ->select('client_course_comments.*')
                ->orderBy('client_course_comments.id', 'desc')->get();
        }
    }

    protected static function deleteClientCourseCommentsByClientId($clientId){
        $comments = static::where('client_id', $clientId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
        return;
    }

    protected static function deleteClientCourseCommentsByClientIdByUserId($clientId, $userId){
        $comments = static::where('client_id', $clientId)->where('user_id', $userId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
        return;
    }
}
